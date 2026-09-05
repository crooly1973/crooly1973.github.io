<?php
/**
 * VORLAGE für die geheime Konfiguration der Fitbit-/Google-Health-Anbindung.
 *
 * SO GEHT'S:
 *  1) Diese Datei auf dem IONOS-Webspace KOPIEREN und die Kopie
 *     "health-config.php" nennen (ohne ".sample").
 *  2) In der Kopie den echten Client-Schlüssel bei 'client_secret' eintragen.
 *
 * WICHTIG: Die fertige "health-config.php" NICHT in GitHub hochladen –
 * sie enthält das Geheimnis und bleibt nur auf dem Server.
 * (Diese Vorlage hier enthält KEIN Geheimnis und darf offen liegen.)
 */
return [
    // Öffentlich, darf offen stehen:
    'client_id'     => '618080078275-11tqb11r6lbciu01sv6tisohmbg28g61.apps.googleusercontent.com',

    // GEHEIM – hier den Client-Schlüssel aus der Google Cloud eintragen:
    'client_secret' => 'HIER-DEIN-CLIENT-SCHLUESSEL-EINTRAGEN',

    // Muss exakt mit der in der Google Cloud hinterlegten Weiterleitungs-URI übereinstimmen:
    'redirect_uri'  => 'https://oliver-rock.de/gesundheitsapp/health-callback.php',
];
