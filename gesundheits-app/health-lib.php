<?php
/**
 * VITARA – gemeinsame Helfer für die Google-Health-Anbindung.
 * Enthält KEINE Geheimnisse (die stehen nur in health-config.php).
 */

/** Lädt die Server-Konfiguration (health-config.php) oder null, wenn sie fehlt. */
function vitara_config() {
    $p = __DIR__ . '/health-config.php';
    if (!file_exists($p)) return null;
    $cfg = include $p;
    return is_array($cfg) ? $cfg : null;
}

/** Prüft, ob die Konfiguration vollständig ist. */
function vitara_config_ok($cfg) {
    return $cfg
        && !empty($cfg['client_id'])
        && !empty($cfg['client_secret'])
        && $cfg['client_secret'] !== 'HIER-DEIN-CLIENT-SCHLUESSEL-EINTRAGEN'
        && !empty($cfg['redirect_uri']);
}

/** Nicht erratbarer Dateiname für die Token (aus dem geheimen Schlüssel abgeleitet) -> Schutz ohne .htaccess. */
function vitara_token_path($cfg) {
    $suffix = substr(hash('sha256', 'vitara-tokens|' . ($cfg['client_secret'] ?? 'x')), 0, 24);
    return __DIR__ . '/health-tokens-' . $suffix . '.json';
}

/** Die Berechtigungen (Scopes), die wir von Google anfragen. */
function vitara_scopes() {
    return array(
        'https://www.googleapis.com/auth/googlehealth.activity_and_fitness.readonly',
        'https://www.googleapis.com/auth/googlehealth.sleep.readonly',
        'https://www.googleapis.com/auth/googlehealth.health_metrics_and_measurements.readonly',
    );
}

/** Einfacher HTTP-Aufruf per cURL. Gibt code/body/err zurück. */
function vitara_http($url, $opts = array()) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);
    foreach ($opts as $k => $v) curl_setopt($ch, $k, $v);
    $body = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return array('code' => $code, 'body' => $body, 'err' => $err);
}

function vitara_load_tokens($cfg) {
    $p = vitara_token_path($cfg);
    if (!file_exists($p)) return null;
    return json_decode(file_get_contents($p), true);
}
function vitara_save_tokens($cfg, $tok) {
    @file_put_contents(vitara_token_path($cfg), json_encode($tok));
}

/** Liefert ein gültiges Access-Token (erneuert es bei Bedarf per Refresh) oder null (+ $err). */
function vitara_access_token($cfg, &$err) {
    $tok = vitara_load_tokens($cfg);
    if (!$tok || empty($tok['access_token'])) { $err = 'nicht_verbunden'; return null; }
    $expires = (isset($tok['obtained_at']) ? $tok['obtained_at'] : 0) + (isset($tok['expires_in']) ? $tok['expires_in'] : 0) - 60;
    if (time() >= $expires) {
        if (empty($tok['refresh_token'])) { $err = 'kein_refresh_token'; return null; }
        $r = vitara_http('https://oauth2.googleapis.com/token', array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query(array(
                'client_id'     => $cfg['client_id'],
                'client_secret' => $cfg['client_secret'],
                'refresh_token' => $tok['refresh_token'],
                'grant_type'    => 'refresh_token',
            )),
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        ));
        $new = json_decode($r['body'], true);
        if ($r['code'] !== 200 || empty($new['access_token'])) { $err = 'refresh_fehler:' . $r['code']; return null; }
        $tok['access_token'] = $new['access_token'];
        $tok['expires_in']   = isset($new['expires_in']) ? $new['expires_in'] : 3600;
        $tok['obtained_at']  = time();
        if (!empty($new['refresh_token'])) $tok['refresh_token'] = $new['refresh_token'];
        vitara_save_tokens($cfg, $tok);
    }
    return $tok['access_token'];
}

/** Roh-Abruf eines Datentyps. $filter = fertiger Filter-String oder null (ohne Filter). */
function vitara_health_get_raw($access, $dataType, $filter = null, $pageSize = 100) {
    $url = 'https://health.googleapis.com/v4/users/me/dataTypes/' . rawurlencode($dataType)
         . '/dataPoints?page_size=' . intval($pageSize);
    if ($filter) $url .= '&filter=' . rawurlencode($filter);
    return vitara_http($url, array(CURLOPT_HTTPHEADER => array('Authorization: Bearer ' . $access)));
}

/** Ruft einen Datentyp für einen Zeitraum ab. Filter-Anfang ist der Datentyp-Name. */
function vitara_health_get($access, $dataType, $start, $end) {
    $filter = $dataType . '.interval.start_time >= "' . $start . '" AND ' . $dataType . '.interval.start_time <= "' . $end . '"';
    return vitara_health_get_raw($access, $dataType, $filter, 100);
}
