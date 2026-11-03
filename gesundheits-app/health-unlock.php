<?php
/**
 * VITARA – entsperrt dieses Gerät einmalig mit dem App-Schlüssel ('app_key' aus health-config.php).
 * Danach merkt sich das Gerät den Schlüssel als sicheres Cookie (ca. 1 Jahr).
 * Auf dem iPhone bitte direkt in der Startbildschirm-App entsperren (eigener Speicher).
 */
require_once __DIR__ . '/health-lib.php';
header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');

$cfg = vitara_config();
$meldung = '';

if (!vitara_key_active($cfg)) {
    $meldung = 'Der Schutz ist noch nicht eingeschaltet (kein <b>app_key</b> in health-config.php). Die App funktioniert trotzdem.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $k = isset($_POST['key']) ? trim((string)$_POST['key']) : '';
    if (vitara_key_matches($cfg, 'app_key', $k)) {
        vitara_set_key_cookie($k);
        header('Location: index.html?entsperrt=1');
        exit;
    }
    sleep(1);   // bremst Rateversuche
    $meldung = 'Der Schlüssel passt nicht. Bitte noch einmal.';
}
?>
<meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1">
<title>VITARA entsperren</title>
<body style="font-family:system-ui;max-width:440px;margin:40px auto;padding:0 16px;line-height:1.55;color:#0f2839">
<h2>🔒 VITARA entsperren</h2>
<p>Deine Gesundheitsdaten sind mit einem Schlüssel geschützt. Gib ihn einmal auf diesem Gerät ein.</p>
<?php if ($meldung) echo '<p style="background:#fff4e5;padding:10px 12px;border-radius:10px">' . $meldung . '</p>'; ?>
<?php if (vitara_key_active($cfg)) { ?>
<form method="post" autocomplete="off">
  <input type="password" name="key" required autofocus placeholder="Schlüssel"
         style="width:100%;box-sizing:border-box;padding:12px;font-size:16px;border:1px solid #c9d6de;border-radius:10px">
  <button style="margin-top:12px;width:100%;padding:12px;font-size:16px;font-weight:700;color:#fff;background:#06c2cf;border:0;border-radius:10px">Entsperren</button>
</form>
<?php } ?>
<p style="margin-top:20px"><a href="index.html">Zur App</a></p>
</body>
