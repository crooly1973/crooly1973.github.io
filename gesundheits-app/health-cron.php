<?php
/**
 * VITARA – automatischer täglicher Abgleich (für IONOS-Cronjob).
 * Holt die aktuellen Werte von Google Health und speichert sie in der Datenbank –
 * OHNE dass die App geöffnet werden muss. Läuft mit den gespeicherten Token.
 *
 * Einrichtung: als IONOS-Cronjob 1× täglich (z.B. 00:30) ausführen:
 *   php /Oliver/gesundheitsapp/health-cron.php
 * oder per URL aufrufen:
 *   https://oliver-rock.de/gesundheitsapp/health-cron.php
 *
 * Gibt keine Gesundheitsdaten aus (nur ein kurzes OK/Fehler), damit nichts Sensibles im Log landet.
 */

$_GET = array();            // kein Debug, keine Parameter
ob_start();
require __DIR__ . '/health-data.php';   // führt Abruf + Speichern aus
$json = ob_get_clean();

$d = json_decode($json, true);
header('Content-Type: text/plain; charset=utf-8');
if (is_array($d) && !empty($d['ok'])) {
    echo 'OK ' . gmdate('Y-m-d H:i:s') . " UTC – gespeichert (Speicher: " . (isset($d['speicher']) ? $d['speicher'] : '?') . ").\n";
} else {
    echo 'FEHLER ' . gmdate('Y-m-d H:i:s') . " UTC – " . (is_array($d) && isset($d['error']) ? $d['error'] : 'unbekannt') . "\n";
}
