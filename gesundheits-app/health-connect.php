<?php
/**
 * VITARA – startet die Anmeldung bei Google (Schritt 1 des OAuth-Ablaufs).
 * Leitet den Nutzer zur Google-Zustimmungsseite weiter.
 */
require __DIR__ . '/health-lib.php';

$cfg = vitara_config();
if (!vitara_config_ok($cfg)) {
    header('Content-Type: text/html; charset=utf-8');
    echo '<meta charset=utf-8><body style="font-family:system-ui;max-width:520px;margin:40px auto;padding:0 16px">'
       . '<h2>Konfiguration fehlt</h2><p>Bitte lege auf dem Server <b>health-config.php</b> an und trage den Client-Schlüssel ein '
       . '(Vorlage: health-config.sample.php).</p></body>';
    exit;
}

$state = bin2hex(random_bytes(16));
setcookie('vitara_state', $state, array(
    'expires'  => time() + 600,
    'path'     => '/',
    'secure'   => true,
    'httponly' => true,
    'samesite' => 'Lax',
));

$params = http_build_query(array(
    'client_id'              => $cfg['client_id'],
    'redirect_uri'           => $cfg['redirect_uri'],
    'response_type'          => 'code',
    'scope'                  => implode(' ', vitara_scopes()),
    'access_type'            => 'offline',       // damit wir ein Refresh-Token bekommen
    'include_granted_scopes' => 'true',
    'prompt'                 => 'consent',
    'state'                  => $state,
));

header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
