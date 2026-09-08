<?php
/**
 * VITARA – liefert den gespeicherten Verlauf aus der Datenbank als JSON an die App.
 * { ok:true, speicher:"sqlite|json", daten: { "vital.hrv": {"2026-09-05":"41", ...}, ... } }
 */
require __DIR__ . '/health-lib.php';
require __DIR__ . '/health-store.php';
header('Content-Type: application/json; charset=utf-8');

$cfg = vitara_config();
if (!vitara_config_ok($cfg)) { echo json_encode(array('ok' => false, 'error' => 'konfig_fehlt')); exit; }

$days = isset($_GET['days']) ? intval($_GET['days']) : 120;

// ?all=1 -> kompletter Speicher (auch Gelenke, Präparate, Training, manuelle Werte)
if (isset($_GET['all'])) {
    echo json_encode(array('ok' => true, 'speicher' => vitara_db_kind(), 'daten' => vitara_history_all()));
    exit;
}

$felder = array(
    'vital.schritte', 'vital.hr', 'vital.puls', 'vital.hrv', 'vital.atem', 'vital.azm',
    'vital.schlafMin', 'vital.schlafTief', 'vital.schlafLeicht', 'vital.schlafRem', 'vital.schlafWach',
);
$out = array('ok' => true, 'speicher' => vitara_db_kind(), 'daten' => array());
foreach ($felder as $f) { $out['daten'][$f] = vitara_history($f, $days); }
echo json_encode($out);
