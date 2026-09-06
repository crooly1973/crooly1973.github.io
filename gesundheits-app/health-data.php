<?php
/**
 * VITARA – liefert die aktuellen Werte aus Google Health als JSON an die App.
 * Nutzt die gespeicherten Token (erneuert sie bei Bedarf). Kein Neu-Anmelden nötig.
 * Mit ?debug=1 werden zusätzlich Roh-Antworten mitgeliefert.
 */
require __DIR__ . '/health-lib.php';
header('Content-Type: application/json; charset=utf-8');

$debug = isset($_GET['debug']);
$out = array('ok' => false);

$cfg = vitara_config();
if (!vitara_config_ok($cfg)) { $out['error'] = 'konfig_fehlt'; echo json_encode($out); exit; }

$err = '';
$access = vitara_access_token($cfg, $err);
if (!$access) { $out['error'] = $err; echo json_encode($out); exit; }

// Heutiges Zeitfenster in Berliner Zeit -> UTC
$tz = new DateTimeZone('Europe/Berlin');
$utc = new DateTimeZone('UTC');
$startLocal = new DateTime('today 00:00:00', $tz);
$endLocal   = new DateTime('tomorrow 00:00:00', $tz);
$startUTC = (clone $startLocal)->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
$endUTC   = (clone $endLocal)->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');
$startUTC7 = (clone $startLocal)->modify('-6 days')->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');

$dbg = array();

// ---- Schritte (Intervall-Typ, kein Bindestrich -> Filter erlaubt): Tag summieren ----
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
if ($gotAny) $schritte = $total;

// ---- Herzfrequenz (Sample-Typ mit Bindestrich -> kein Filter): neueste Messung ----
$hr = null;
$rHr = vitara_health_get_raw($access, 'heart-rate', null, 1);
if ($debug) $dbg['hr'] = array('code' => $rHr['code'], 'body' => substr((string)$rHr['body'], 0, 800));
if ($rHr['code'] === 200) {
    $j = json_decode($rHr['body'], true);
    if (!empty($j['dataPoints'])) {
        $dp = $j['dataPoints'][0];
        if (isset($dp['heartRate']['beatsPerMinute'])) $hr = (int)round($dp['heartRate']['beatsPerMinute']);
        else { $v = vitara_first_number($dp); if ($v !== null) $hr = (int)round($v); }
    }
}

// ---- Tageswerte (Bindestrich -> kein Filter): neuesten Punkt nehmen ----
function vitara_daily_first($access, $dataType, &$raw) {
    $r = vitara_health_get_raw($access, $dataType, null, 10);
    $raw = array('code' => $r['code'], 'body' => substr((string)$r['body'], 0, 700));
    if ($r['code'] !== 200) return null;
    $j = json_decode($r['body'], true);
    return empty($j['dataPoints']) ? null : $j['dataPoints'][0];   // Liste ist neueste-zuerst
}
function vitara_first_number($node, $depth = 0) {
    if ($depth > 6) return null;
    if (is_numeric($node)) return $node + 0;
    if (is_array($node)) foreach ($node as $k => $v) {
        if (in_array($k, array('dataSource','interval','dataType','date','startUtcOffset','endUtcOffset','civilStartTime','civilEndTime','year','month','day','hours','minutes','seconds','nanos'), true)) continue;
        $n = vitara_first_number($v, $depth + 1); if ($n !== null) return $n;
    }
    return null;
}

$rhr = null; $dpR = vitara_daily_first($access, 'daily-resting-heart-rate', $rawR);
if ($dpR && isset($dpR['dailyRestingHeartRate']['beatsPerMinute'])) $rhr = (int)round($dpR['dailyRestingHeartRate']['beatsPerMinute']);
if ($debug) $dbg['rhr'] = $rawR;

$hrv = null; $dpH = vitara_daily_first($access, 'daily-heart-rate-variability', $rawH);
if ($dpH && isset($dpH['dailyHeartRateVariability']['averageHeartRateVariabilityMilliseconds'])) $hrv = (int)round($dpH['dailyHeartRateVariability']['averageHeartRateVariabilityMilliseconds']);
if ($debug) $dbg['hrv'] = $rawH;

$atem = null; $dpA = vitara_daily_first($access, 'daily-respiratory-rate', $rawA);
if ($dpA && isset($dpA['dailyRespiratoryRate']['breathsPerMinute'])) $atem = round(($dpA['dailyRespiratoryRate']['breathsPerMinute']) * 10) / 10;
if ($debug) $dbg['atem'] = $rawA;

// ---- Aktivzonenminuten (Intervall-Typ mit Bindestrich -> kein Filter): neuesten Tag summieren ----
$azm = null;
$rZ = vitara_health_get_raw($access, 'active-zone-minutes', null, 1000);
if ($debug) $dbg['azm'] = array('code' => $rZ['code'], 'body' => substr((string)$rZ['body'], 0, 700));
if ($rZ['code'] === 200) {
    $j = json_decode($rZ['body'], true);
    if (!empty($j['dataPoints'])) {
        $zielDatum = null; $summe = 0;
        foreach ($j['dataPoints'] as $dp) {
            $d = isset($dp['activeZoneMinutes']['interval']['civilStartTime']['date']) ? $dp['activeZoneMinutes']['interval']['civilStartTime']['date'] : null;
            if (!$d) continue;
            $tag = $d['year'] . '-' . $d['month'] . '-' . $d['day'];
            if ($zielDatum === null) $zielDatum = $tag;         // neuester Tag mit Daten
            if ($tag !== $zielDatum) break;                     // Liste ist neueste-zuerst -> fertig
            if (isset($dp['activeZoneMinutes']['activeZoneMinutes'])) $summe += intval($dp['activeZoneMinutes']['activeZoneMinutes']);
        }
        $azm = $summe;
    }
}

// ---- Schlaf (Sitzungs-Typ, kein Bindestrich -> Filter erlaubt): neueste Nacht ----
$schlafMin = null; $schlaf = null;
$sfilter = 'sleep.interval.end_time >= "' . $startUTC7 . '" AND sleep.interval.end_time < "' . $endUTC . '"';
$rs = vitara_health_get_raw($access, 'sleep', $sfilter, 50);
if ($debug) $dbg['sleep'] = array('code' => $rs['code'], 'body' => substr((string)$rs['body'], 0, 1200));
if ($rs['code'] === 200) {
    $j = json_decode($rs['body'], true);
    if (!empty($j['dataPoints'])) {
        // neueste Hauptschlaf-Sitzung wählen (mainSleep bevorzugt, sonst erste)
        $sess = null;
        foreach ($j['dataPoints'] as $dp) { if (!empty($dp['sleep']['metadata']['mainSleep'])) { $sess = $dp; break; } }
        if (!$sess) $sess = $j['dataPoints'][0];
        $sum = isset($sess['sleep']['summary']) ? $sess['sleep']['summary'] : null;
        if ($sum) {
            if (isset($sum['minutesAsleep'])) $schlafMin = intval($sum['minutesAsleep']);
            elseif (isset($sum['minutesInSleepPeriod'])) $schlafMin = intval($sum['minutesInSleepPeriod']);
            if (!empty($sum['stagesSummary'])) {
                $map = array('DEEP' => 'tief', 'LIGHT' => 'leicht', 'REM' => 'rem', 'WAKE' => 'wach', 'AWAKE' => 'wach');
                $st = array();
                foreach ($sum['stagesSummary'] as $stg) {
                    $t = isset($stg['type']) ? strtoupper($stg['type']) : '';
                    if (isset($map[$t]) && isset($stg['minutes'])) $st[$map[$t]] = intval($stg['minutes']);
                }
                if ($st) $schlaf = $st;   // echte Phasen (nur bei STAGES-Nächten vorhanden)
            }
        }
    }
}

$out['ok'] = true;
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
