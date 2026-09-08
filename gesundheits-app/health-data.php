<?php
/**
 * VITARA – holt aktuelle Werte aus Google Health, SPEICHERT sie in der Datenbank
 * und liefert sie als JSON an die App. Die Tages-Listen (Ruhepuls/HRV/Atem/Schlaf/Aktivzonen)
 * enthalten mehrere Tage -> damit füllt sich der Verlauf automatisch (Backfill).
 * Mit ?debug=1 werden zusätzlich Roh-Antworten mitgeliefert.
 */
require __DIR__ . '/health-lib.php';
require __DIR__ . '/health-store.php';
header('Content-Type: application/json; charset=utf-8');

$debug = isset($_GET['debug']);
$out = array('ok' => false);

$cfg = vitara_config();
if (!vitara_config_ok($cfg)) { $out['error'] = 'konfig_fehlt'; echo json_encode($out); exit; }

$err = '';
$access = vitara_access_token($cfg, $err);
if (!$access) { $out['error'] = $err; echo json_encode($out); exit; }

$tz = new DateTimeZone('Europe/Berlin');
$utc = new DateTimeZone('UTC');
$startLocal = new DateTime('today 00:00:00', $tz);
$endLocal   = new DateTime('tomorrow 00:00:00', $tz);
$heuteDatum = $startLocal->format('Y-m-d');
$startUTC = (clone $startLocal)->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
$endUTC   = (clone $endLocal)->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
$startUTC7 = (clone $startLocal)->modify('-30 days')->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');

$dbg = array();

function vitara_first_number($node, $depth = 0) {
    if ($depth > 6) return null;
    if (is_numeric($node)) return $node + 0;
    if (is_array($node)) foreach ($node as $k => $v) {
        if (in_array($k, array('dataSource','interval','dataType','date','startUtcOffset','endUtcOffset','civilStartTime','civilEndTime','year','month','day','hours','minutes','seconds','nanos'), true)) continue;
        $n = vitara_first_number($v, $depth + 1); if ($n !== null) return $n;
    }
    return null;
}
function vitara_datum_aus($d) { return sprintf('%04d-%02d-%02d', $d['year'], $d['month'], $d['day']); }

// ---- Schritte (heute) ----
$schritte = null; $total = 0; $pageToken = null; $pages = 0; $gotAny = false;
do {
    $filter = 'steps.interval.start_time >= "' . $startUTC . '" AND steps.interval.start_time < "' . $endUTC . '"';
    $url = 'https://health.googleapis.com/v4/users/me/dataTypes/steps/dataPoints?page_size=1000&filter=' . rawurlencode($filter);
    if ($pageToken) $url .= '&pageToken=' . rawurlencode($pageToken);
    $r = vitara_http($url, array(CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . $access)));
    if ($r['code'] !== 200) { if ($debug) $dbg['steps'] = $r['body']; break; }
    $j = json_decode($r['body'], true);
    if (!empty($j['dataPoints'])) { $gotAny = true; foreach ($j['dataPoints'] as $dp) { if (isset($dp['steps']['count'])) $total += intval($dp['steps']['count']); } }
    $pageToken = isset($j['nextPageToken']) ? $j['nextPageToken'] : null; $pages++;
} while ($pageToken && $pages < 20);
if ($gotAny) { $schritte = $total; }

// ---- Schritte-Rückblick (Backfill): Tages-Summen der letzten 30 Tage via dailyRollUp ----
$startLocal30 = (clone $startLocal)->modify('-30 days');
$rSr = vitara_daily_rollup($access, 'steps', $startLocal30, $endLocal);
if ($debug) $dbg['stepsRollup'] = array('code' => $rSr['code'], 'body' => substr((string)$rSr['body'], 0, 700));
if ($rSr['code'] === 200) {
    $j = json_decode($rSr['body'], true);
    if (!empty($j['dataPoints'])) foreach ($j['dataPoints'] as $dp) {
        $iv = isset($dp['steps']['interval']) ? $dp['steps']['interval'] : (isset($dp['interval']) ? $dp['interval'] : null);
        $datum = null;
        if ($iv && isset($iv['civilStartTime']['date'])) $datum = vitara_datum_aus($iv['civilStartTime']['date']);
        elseif ($iv && isset($iv['startTime'])) $datum = gmdate('Y-m-d', strtotime($iv['startTime']) + (isset($iv['startUtcOffset']) ? intval($iv['startUtcOffset']) : 0));
        if ($datum && isset($dp['steps']['count'])) vitara_store($datum, 'vital.schritte', intval($dp['steps']['count']));
    }
}
// heutigen (Live-)Wert zuletzt schreiben, damit er den Rollup-Wert für heute überschreibt
if ($schritte !== null) vitara_store($heuteDatum, 'vital.schritte', $schritte);

// ---- Herzfrequenz (neueste Messung, Momentaufnahme unter heute) ----
$hr = null;
$rHr = vitara_health_get_raw($access, 'heart-rate', null, 1);
if ($debug) $dbg['hr'] = array('code' => $rHr['code'], 'body' => substr((string)$rHr['body'], 0, 600));
if ($rHr['code'] === 200) {
    $j = json_decode($rHr['body'], true);
    if (!empty($j['dataPoints'])) {
        $dp = $j['dataPoints'][0];
        $hr = isset($dp['heartRate']['beatsPerMinute']) ? (int)round($dp['heartRate']['beatsPerMinute']) : (($v = vitara_first_number($dp)) !== null ? (int)round($v) : null);
    }
}
if ($hr !== null) vitara_store($heuteDatum, 'vital.hr', $hr);

// ---- Tageswerte: alle zurückgelieferten Tage speichern (Backfill) + neuesten zurückgeben ----
function vitara_daily_store($access, $dataType, $feld, $payloadKey, $valueKey, &$raw) {
    $r = vitara_health_get_raw($access, $dataType, null, 40);
    $raw = array('code' => $r['code'], 'body' => substr((string)$r['body'], 0, 600));
    if ($r['code'] !== 200) return null;
    $j = json_decode($r['body'], true);
    if (empty($j['dataPoints'])) return null;
    $latest = null;
    foreach ($j['dataPoints'] as $dp) {
        $o = isset($dp[$payloadKey]) ? $dp[$payloadKey] : null;
        if (!$o || !isset($o[$valueKey]) || !isset($o['date'])) continue;
        $datum = vitara_datum_aus($o['date']);
        vitara_store($datum, $feld, $o[$valueKey]);
        if ($latest === null) $latest = $o[$valueKey];   // erster = neuester
    }
    return $latest;
}

$rhr = vitara_daily_store($access, 'daily-resting-heart-rate', 'vital.puls', 'dailyRestingHeartRate', 'beatsPerMinute', $rawR);
if ($rhr !== null) $rhr = (int)round($rhr);
if ($debug) $dbg['rhr'] = $rawR;

$hrv = vitara_daily_store($access, 'daily-heart-rate-variability', 'vital.hrv', 'dailyHeartRateVariability', 'averageHeartRateVariabilityMilliseconds', $rawH);
if ($hrv !== null) $hrv = (int)round($hrv);
if ($debug) $dbg['hrv'] = $rawH;

$atem = vitara_daily_store($access, 'daily-respiratory-rate', 'vital.atem', 'dailyRespiratoryRate', 'breathsPerMinute', $rawA);
if ($atem !== null) $atem = round($atem * 10) / 10;
if ($debug) $dbg['atem'] = $rawA;

// ---- Aktivzonenminuten: je Tag summieren & speichern, neuesten Tag zurückgeben ----
$azm = null;
$rZ = vitara_health_get_raw($access, 'active-zone-minutes', null, 1000);
if ($debug) $dbg['azm'] = array('code' => $rZ['code'], 'body' => substr((string)$rZ['body'], 0, 500));
if ($rZ['code'] === 200) {
    $j = json_decode($rZ['body'], true);
    if (!empty($j['dataPoints'])) {
        $proTag = array(); $reihenfolge = array();
        foreach ($j['dataPoints'] as $dp) {
            $iv = isset($dp['activeZoneMinutes']['interval']['civilStartTime']['date']) ? $dp['activeZoneMinutes']['interval']['civilStartTime']['date'] : null;
            if (!$iv) continue;
            $datum = vitara_datum_aus($iv);
            if (!isset($proTag[$datum])) { $proTag[$datum] = 0; $reihenfolge[] = $datum; }
            if (isset($dp['activeZoneMinutes']['activeZoneMinutes'])) $proTag[$datum] += intval($dp['activeZoneMinutes']['activeZoneMinutes']);
        }
        foreach ($proTag as $datum => $sum) vitara_store($datum, 'vital.azm', $sum);
        if (!empty($reihenfolge)) $azm = $proTag[$reihenfolge[0]];   // neuester Tag
    }
}

// ---- Schlaf: jede Nacht speichern (Dauer + Phasen), neueste Hauptschlaf-Nacht zurückgeben ----
$schlafMin = null; $schlaf = null;
$sfilter = 'sleep.interval.end_time >= "' . $startUTC7 . '" AND sleep.interval.end_time < "' . $endUTC . '"';
$rs = vitara_health_get_raw($access, 'sleep', $sfilter, 60);
if ($debug) $dbg['sleep'] = array('code' => $rs['code'], 'body' => substr((string)$rs['body'], 0, 900));
if ($rs['code'] === 200) {
    $j = json_decode($rs['body'], true);
    if (!empty($j['dataPoints'])) {
        $stageMap = array('DEEP' => 'schlafTief', 'LIGHT' => 'schlafLeicht', 'REM' => 'schlafRem', 'WAKE' => 'schlafWach', 'AWAKE' => 'schlafWach');
        $newestMain = null;
        foreach ($j['dataPoints'] as $dp) {
            if (!isset($dp['sleep']['summary'])) continue;
            $sl = $dp['sleep']; $sum = $sl['summary'];
            // Datum der Nacht = lokales Aufwach-Datum (Ende)
            $datum = $heuteDatum;
            if (isset($sl['interval']['endTime'])) {
                $off = isset($sl['interval']['endUtcOffset']) ? intval($sl['interval']['endUtcOffset']) : 0;
                $datum = gmdate('Y-m-d', strtotime($sl['interval']['endTime']) + $off);
            }
            $mins = isset($sum['minutesAsleep']) ? intval($sum['minutesAsleep']) : (isset($sum['minutesInSleepPeriod']) ? intval($sum['minutesInSleepPeriod']) : null);
            if ($mins !== null) vitara_store($datum, 'vital.schlafMin', $mins);
            $stg = array();
            if (!empty($sum['stagesSummary'])) foreach ($sum['stagesSummary'] as $s) {
                $t = isset($s['type']) ? strtoupper($s['type']) : '';
                if (isset($stageMap[$t]) && isset($s['minutes'])) { $stg[$stageMap[$t]] = intval($s['minutes']); vitara_store($datum, 'vital.' . $stageMap[$t], intval($s['minutes'])); }
            }
            // neueste Hauptschlaf-Nacht für die Anzeige merken
            if ($newestMain === null && !empty($sl['metadata']['mainSleep'])) {
                $newestMain = true; $schlafMin = $mins;
                if ($stg) { $schlaf = array('tief' => isset($stg['schlafTief']) ? $stg['schlafTief'] : null, 'leicht' => isset($stg['schlafLeicht']) ? $stg['schlafLeicht'] : null, 'rem' => isset($stg['schlafRem']) ? $stg['schlafRem'] : null, 'wach' => isset($stg['schlafWach']) ? $stg['schlafWach'] : null); }
            }
        }
        if ($schlafMin === null && !empty($j['dataPoints'][0]['sleep']['summary']['minutesAsleep'])) $schlafMin = intval($j['dataPoints'][0]['sleep']['summary']['minutesAsleep']);
    }
}

$out['ok'] = true;
$out['speicher'] = vitara_db_kind();
$out['schritte'] = $schritte;
$out['hr'] = $hr;
$out['rhr'] = $rhr;
$out['hrv'] = $hrv;
$out['atem'] = $atem;
$out['azm'] = $azm;
$out['schlafMin'] = $schlafMin;
$out['schlaf'] = $schlaf;
$out['stand'] = gmdate('Y-m-d\TH:i:s\Z');
if ($debug) $out['debug'] = $dbg;

echo json_encode($out);
