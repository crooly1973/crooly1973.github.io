<?php
/**
 * VITARA – Diagnose: ruft mit den gespeicherten Token echte Werte ab und zeigt die Roh-Antworten.
 * Nur zum Testen der Datenstruktur. Wird nach dem Test wieder entfernt.
 */
require __DIR__ . '/health-lib.php';
header('Content-Type: text/plain; charset=utf-8');

$cfg = vitara_config();
if (!vitara_config_ok($cfg)) { echo "Konfiguration fehlt (health-config.php)."; exit; }

$err = '';
$access = vitara_access_token($cfg, $err);
if (!$access) { echo "Kein gültiges Token: $err\nBitte zuerst über health-connect.php verbinden."; exit; }

echo "VITARA – Daten-Diagnose\n=======================\n\n";

$start = gmdate('Y-m-d\T00:00:00\Z', strtotime('-2 days'));   // letzte 3 Tage
$end   = gmdate('Y-m-d\T23:59:59\Z');
echo "Zeitraum: $start  bis  $end\n\n";

$types = array('steps', 'sleep', 'daily-resting-heart-rate', 'daily-heart-rate-variability');

foreach ($types as $t) {
    echo "==================== $t ====================\n";
    // Versuch A: mit Zeitfilter (Filter-Anfang = Datentyp-Name)
    $filter = $t . '.interval.start_time >= "' . $start . '" AND ' . $t . '.interval.start_time <= "' . $end . '"';
    $a = vitara_health_get_raw($access, $t, $filter, 20);
    echo "[A mit Zeitfilter]  HTTP " . $a['code'] . "\n";
    echo substr((string)$a['body'], 0, 1100) . "\n";
    if ($a['code'] !== 200) {
        // Versuch B: ganz ohne Filter, nur die neuesten Punkte (zeigt die Struktur)
        $b = vitara_health_get_raw($access, $t, null, 5);
        echo "\n[B ohne Filter]     HTTP " . $b['code'] . "\n";
        echo substr((string)$b['body'], 0, 1100) . "\n";
    }
    echo "\n\n";
}

echo "Fertig.\n";
