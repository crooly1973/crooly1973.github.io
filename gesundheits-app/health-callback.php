<?php
/**
 * VITARA – Google leitet nach der Zustimmung hierher zurück (Schritt 2 des OAuth-Ablaufs).
 * Tauscht den Code gegen Token, speichert sie und macht einen Test-Abruf (Schritte heute).
 */
require __DIR__ . '/health-lib.php';
header('Content-Type: text/html; charset=utf-8');

function seite($html) {
    echo '<meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1">'
       . '<body style="font-family:system-ui;max-width:560px;margin:36px auto;padding:0 16px;line-height:1.55;color:#0f2839">'
       . $html . '</body>';
    exit;
}

$cfg = vitara_config();
if (!vitara_config_ok($cfg)) seite('<h2>Konfiguration fehlt</h2><p>Bitte health-config.php anlegen.</p>');

if (isset($_GET['error'])) {
    seite('<h2>Abgebrochen</h2><p>Google meldet: <b>' . htmlspecialchars($_GET['error']) . '</b></p>'
        . '<p><a href="health-connect.php">Nochmal versuchen</a> · <a href="index.html">Zur App</a></p>');
}
if (empty($_GET['code'])) seite('<h2>Fehler</h2><p>Kein Code von Google erhalten.</p>');

$state = isset($_GET['state']) ? $_GET['state'] : '';
$cookie = isset($_COOKIE['vitara_state']) ? $_COOKIE['vitara_state'] : '';
if (!$state || $state !== $cookie) {
    seite('<h2>Sicherheits-Check fehlgeschlagen</h2><p>Der „state" passt nicht (evtl. Cookie blockiert). Bitte erneut:</p>'
        . '<p><a href="health-connect.php">Nochmal verbinden</a></p>');
}

// Code gegen Token tauschen
$r = vitara_http('https://oauth2.googleapis.com/token', array(
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query(array(
        'code'          => $_GET['code'],
        'client_id'     => $cfg['client_id'],
        'client_secret' => $cfg['client_secret'],
        'redirect_uri'  => $cfg['redirect_uri'],
        'grant_type'    => 'authorization_code',
    )),
    CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
));
$tok = json_decode($r['body'], true);
if ($r['code'] !== 200 || empty($tok['access_token'])) {
    seite('<h2>Token-Fehler (HTTP ' . intval($r['code']) . ')</h2>'
        . '<pre style="white-space:pre-wrap;background:#f4f4f4;padding:10px;border-radius:8px;font-size:12px">'
        . htmlspecialchars($r['body']) . '</pre><p><a href="health-connect.php">Nochmal versuchen</a></p>');
}
$tok['obtained_at'] = time();
vitara_save_tokens($cfg, $tok);

$hasRefresh = !empty($tok['refresh_token']) ? 'JA ✓' : 'NEIN (bitte Rechte in Google entfernen und neu verbinden)';
$scopes = isset($tok['scope']) ? htmlspecialchars($tok['scope']) : '(keine Angabe)';

// Test-Abruf: Schritte heute (zeigt uns die Datenstruktur)
$start = gmdate('Y-m-d\T00:00:00\Z');
$end   = gmdate('Y-m-d\T23:59:59\Z');
$test  = vitara_health_get($tok['access_token'], 'steps', $start, $end);
$snip  = htmlspecialchars(substr((string)$test['body'], 0, 1800));

seite(
    '<h2>✅ Verbindung hergestellt</h2>'
    . '<p><b>Aktualisierungs-Token:</b> ' . $hasRefresh . '</p>'
    . '<p><b>Freigegebene Rechte:</b><br><small style="color:#4a6170">' . $scopes . '</small></p>'
    . '<hr><p><b>Test-Abruf „Schritte heute" — HTTP ' . intval($test['code']) . '</b></p>'
    . '<pre style="white-space:pre-wrap;background:#f4f4f4;padding:10px;border-radius:8px;font-size:12px">' . $snip . '</pre>'
    . '<p style="margin-top:20px"><a href="index.html?fitbit=verbunden" style="display:inline-block;background:#06c2cf;color:#fff;padding:10px 18px;border-radius:10px;text-decoration:none;font-weight:700">Zurück zur App</a></p>'
);
