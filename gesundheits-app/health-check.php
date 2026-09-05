<?php
/**
 * VITARA – Server-Selbsttest
 * Prüft, ob dieser Webspace alles kann, was die Fitbit-/Google-Health-Anbindung braucht:
 *  - PHP läuft
 *  - cURL & JSON vorhanden (für die Google-Aufrufe)
 *  - der Ordner ist beschreibbar (zum Speichern der Anmelde-Token)
 *  - der Server erreicht Google
 *  - die geheime Konfigurationsdatei (health-config.php) liegt bereit
 * Gibt KEINE geheimen Werte aus. Diese Datei darf offen im Web stehen.
 */
header('Content-Type: text/plain; charset=utf-8');

echo "VITARA – Server-Check\n";
echo "=====================\n\n";

echo "PHP-Version:        " . phpversion() . "\n";
echo "cURL vorhanden:     " . (function_exists('curl_init') ? "JA" : "NEIN  <-- wird gebraucht") . "\n";
echo "JSON vorhanden:     " . (function_exists('json_encode') ? "JA" : "NEIN  <-- wird gebraucht") . "\n\n";

// Schreibtest (für die spätere Token-Datei)
$testfile = __DIR__ . '/.vitara-write-test';
$writable = @file_put_contents($testfile, 'ok') !== false;
@unlink($testfile);
echo "Ordner beschreibbar: " . ($writable ? "JA" : "NEIN  <-- wird gebraucht") . "\n\n";

// Erreichbarkeit von Google
if (function_exists('curl_init')) {
    $ch = curl_init('https://oauth2.googleapis.com/');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    echo "Google erreichbar:   " . ($code ? ("JA (HTTP " . $code . ")") : ("NEIN – " . $err)) . "\n\n";
} else {
    echo "Google erreichbar:   übersprungen (kein cURL)\n\n";
}

// Konfigurationsdatei mit dem geheimen Client-Schlüssel
$cfgPath = __DIR__ . '/health-config.php';
if (file_exists($cfgPath)) {
    $cfg = include $cfgPath;
    $secretOk = !empty($cfg['client_secret']) && $cfg['client_secret'] !== 'HIER-DEIN-CLIENT-SCHLUESSEL-EINTRAGEN';
    echo "health-config.php:   GEFUNDEN\n";
    echo "  client_id:         " . (!empty($cfg['client_id']) ? "gesetzt" : "FEHLT") . "\n";
    echo "  client_secret:     " . ($secretOk ? "gesetzt" : "FEHLT – bitte eintragen") . "\n";
    echo "  redirect_uri:      " . (!empty($cfg['redirect_uri']) ? $cfg['redirect_uri'] : "FEHLT") . "\n";
} else {
    echo "health-config.php:   NICHT gefunden\n";
    echo "  -> Bitte auf dem Server anlegen (Vorlage: health-config.sample.php)\n";
}

echo "\nFertig. Wenn oben überall JA / gesetzt steht, kann ich den Verbinden-Ablauf bauen.\n";
