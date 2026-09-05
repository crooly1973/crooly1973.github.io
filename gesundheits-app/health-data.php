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

// Tageswerte per list + .date-Filter; nimmt den jüngsten Datenpunkt und dessen Zahl.
$d1 = $startLocal7->format('Y-m-d');
$d2 = $endLocal->format('Y-m-d');
function vitara_daily_value($access, $dataType, $d1, $d2, &$raw) {
    // Tages-Typen mit Bindestrich-Namen lassen sich nicht per Filter abfragen -> ohne Filter
    // die neuesten Punkte holen und den jüngsten nehmen. Liefert null, solange keine Daten da sind.
    $r = vitara_health_get_raw($access, $dataType, null, 30);
    $raw = array('code' => $r['code'], 'body' => substr((string)$r['body'], 0, 1800));
    if ($r['code'] !== 200) return null;
    $j = json_decode($r['body'], true);
    if (empty($j['dataPoints'])) return null;
    $dp = end($j['dataPoints']);                       // jüngster Punkt
    return vitara_first_number($dp);
}
// Findet die erste sinnvolle Zahl in der typ-spezifischen Nutzlast eines Datenpunkts.
function vitara_first_number($node, $depth = 0) {
    if ($depth > 6) return null;
    if (is_numeric($node)) return $node + 0;
    if (is_array($node)) {
        foreach ($node as $k => $v) {
            if (in_array($k, array('dataSource', 'interval', 'dataType', 'date', 'startUtcOffset', 'endUtcOffset', 'civilStartTime', 'civilEndTime', 'year', 'month', 'day', 'hours', 'minutes', 'seconds', 'nanos'), true)) continue;
            $n = vitara_first_number($v, $depth + 1);
            if ($n !== null) return $n;
        }
    }
    return null;
}

$rhr = vitara_daily_value($access, 'daily-resting-heart-rate', $d1, $d2, $rawR);
if ($debug) $dbg['rhr'] = $rawR;

$hrv = vitara_daily_value($access, 'daily-heart-rate-variability', $d1, $d2, $rawH);
if ($debug) $dbg['hrv'] = $rawH;

if ($rhr !== null) $rhr = (int)round($rhr);
if ($hrv !== null) $hrv = (int)round($hrv);

$out['ok'] = true;
$out['schritte'] = $schritte;
$out['rhr'] = $rhr;
$out['hrv'] = $hrv;
$out['schlafMin'] = $schlafMin;
$out['schlaf'] = $schlaf;
$out['stand'] = gmdate('Y-m-d\TH:i:s\Z');
if ($debug) $out['debug'] = $dbg;

echo json_encode($out);
