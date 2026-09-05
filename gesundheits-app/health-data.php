<?php
/**
 * VITARA – liefert die heutigen Werte aus Google Health als JSON an die App.
 * Nutzt die gespeicherten Token (erneuert sie bei Bedarf). Kein Neu-Anmelden nötig.
 * Aufruf: fetch('health-data.php')  ->  { ok:true, schritte:..., rhr:..., ... }
 * Mit ?debug=1 werden zusätzlich Roh-Antworten zum Feinschliff mitgeliefert.
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
// Für Tageswerte (Ruhepuls/HRV/Schlaf) etwas Puffer nach hinten
$startLocal7 = (clone $startLocal)->modify('-6 days');
$startUTC7 = (clone $startLocal7)->setTimezone($utc)->format('Y-m-d\TH:i:s\Z');

$dbg = array();

// ---- Schritte (Intervall-Typ): alle Minuten-Punkte des Tages summieren ----
$schritte = null;
$total = 0; $pageToken = null; $pages = 0; $gotAny = false;
do {
    $filter = 'steps.interval.start_time >= "' . $startUTC . '" AND steps.interval.start_time < "' . $endUTC . '"';
    $url = 'https://health.googleapis.com/v4/users/me/dataTypes/steps/dataPoints?page_size=1000&filter=' . rawurlencode($filter);
    if ($pageToken) $url .= '&pageToken=' . rawurlencode($pageToken);
    $r = vitara_http($url, array(CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . $access)));
    if ($r['code'] !== 200) { $dbg['steps'] = $r['body']; break; }
    $j = json_decode($r['body'], true);
    if (!empty($j['dataPoints'])) {
        $gotAny = true;
        foreach ($j['dataPoints'] as $dp) {
            if (isset($dp['steps']['count'])) $total += intval($dp['steps']['count']);
        }
    }
    $pageToken = isset($j['nextPageToken']) ? $j['nextPageToken'] : null;
    $pages++;
} while ($pageToken && $pages < 20);
if ($gotAny) $schritte = $total;

// ---- Schlaf (Sitzungs-Typ): Filter über interval.end_time (7-Tage-Fenster) ----
$schlafMin = null; $schlaf = null;
$sfilter = 'sleep.interval.end_time >= "' . $startUTC7 . '" AND sleep.interval.end_time < "' . $endUTC . '"';
$rs = vitara_health_get_raw($access, 'sleep', $sfilter, 50);
if ($debug) $dbg['sleep'] = array('code' => $rs['code'], 'body' => substr((string)$rs['body'], 0, 1800));

// ---- Ruhepuls (Tages-Typ): über :dailyRollUp ----
$rhr = null;
$rrhr = vitara_daily_rollup($access, 'daily-resting-heart-rate', $startLocal7, $endLocal);
if ($debug) $dbg['rhr'] = array('code' => $rrhr['code'], 'body' => substr((string)$rrhr['body'], 0, 1800));

// ---- HRV (Tages-Typ): über :dailyRollUp ----
$hrv = null;
$rhrv = vitara_daily_rollup($access, 'daily-heart-rate-variability', $startLocal7, $endLocal);
if ($debug) $dbg['hrv'] = array('code' => $rhrv['code'], 'body' => substr((string)$rhrv['body'], 0, 1800));

$out['ok'] = true;
$out['schritte'] = $schritte;
$out['rhr'] = $rhr;
$out['hrv'] = $hrv;
$out['schlafMin'] = $schlafMin;
$out['schlaf'] = $schlaf;
$out['stand'] = gmdate('Y-m-d\TH:i:s\Z');
if ($debug) $out['debug'] = $dbg;

echo json_encode($out);
