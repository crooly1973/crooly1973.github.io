<?php
/**
 * VITARA – Tageswerte für Olivers privates Second Brain (nur lesen).
 * Liefert NUR Schritte, Schlaf (mit Phasen) und Aktivzonenminuten – keine Puls-/HRV-Rohwerte.
 *
 * Aufruf:  brain-export.php?key=<brain_key>&tage=14
 *   (Schlüssel alternativ als Kopfzeile "X-Brain-Key")
 * Eigener Schlüssel 'brain_key' in health-config.php, getrennt vom App-Schlüssel:
 * wer ihn kennt, kann nur diese Zusammenfassung lesen, nichts schreiben.
 * Ohne 'brain_key' ist die Adresse abgeschaltet.
 */
require_once __DIR__ . '/health-lib.php';
require_once __DIR__ . '/health-store.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$cfg = vitara_config();
$given = isset($_SERVER['HTTP_X_BRAIN_KEY']) ? $_SERVER['HTTP_X_BRAIN_KEY'] : (isset($_GET['key']) ? $_GET['key'] : '');
if (!vitara_key_matches($cfg, 'brain_key', $given)) {
    http_response_code(401);
    echo json_encode(array('ok' => false, 'error' => 'schluessel'));
    exit;
}

$tage = isset($_GET['tage']) ? max(1, min(90, intval($_GET['tage']))) : 14;
$felder = array(
    'vital.schritte'     => 'schritte',
    'vital.schlafMin'    => 'schlafMin',
    'vital.schlafTief'   => 'tiefMin',
    'vital.schlafLeicht' => 'leichtMin',
    'vital.schlafRem'    => 'remMin',
    'vital.schlafWach'   => 'wachMin',
    'vital.azm'          => 'aktivzonenMin',
);

$ab = (new DateTime('today', new DateTimeZone('Europe/Berlin')))->modify('-' . ($tage - 1) . ' days')->format('Y-m-d');
$out = array();
foreach ($felder as $feld => $name) {
    foreach (vitara_history($feld, $tage + 5) as $datum => $wert) {
        if ($datum < $ab || $wert === '' || !is_numeric($wert)) continue;
        if (!isset($out[$datum])) $out[$datum] = array();
        $out[$datum][$name] = 0 + $wert;
    }
}
krsort($out);

echo json_encode(array(
    'ok'      => true,
    'quelle'  => 'Fitbit Charge 6 über VITARA (Google Health)',
    'stand'   => gmdate('Y-m-d\TH:i:s\Z'),
    'tage'    => $out,
), JSON_UNESCAPED_UNICODE);
