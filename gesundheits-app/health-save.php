<?php
/**
 * VITARA – speichert einen selbst eingetippten Wert in der Datenbank.
 * Aufruf aus der App: health-save.php?datum=YYYY-MM-DD&feld=schmerz.Rücken&wert=5
 * Nimmt nur bekannte App-Felder an (kein beliebiges Schreiben).
 */
require __DIR__ . '/health-lib.php';
require __DIR__ . '/health-store.php';
header('Content-Type: application/json; charset=utf-8');

$cfg = vitara_config();
if (!vitara_config_ok($cfg)) { echo json_encode(array('ok' => false, 'error' => 'konfig_fehlt')); exit; }

$datum = isset($_GET['datum']) ? $_GET['datum'] : '';
$feld  = isset($_GET['feld'])  ? $_GET['feld']  : '';
$wert  = isset($_GET['wert'])  ? $_GET['wert']  : '';

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum)) { echo json_encode(array('ok' => false, 'error' => 'datum')); exit; }

$erlaubt = array('schmerz.', 'exercise-', 'reps.', 'sets.', 'supp-', 'joint-', 'vital.');
$ok = false; foreach ($erlaubt as $pfx) { if (strpos($feld, $pfx) === 0) { $ok = true; break; } }
if (!$ok || strlen($feld) > 80 || strlen($wert) > 40) { echo json_encode(array('ok' => false, 'error' => 'feld')); exit; }

vitara_store($datum, $feld, $wert);
echo json_encode(array('ok' => true));
