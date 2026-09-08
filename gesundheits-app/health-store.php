<?php
/**
 * VITARA – Datenspeicher (echte Datenbank).
 * Bevorzugt SQLite (eine SQL-Datenbank als einzelne Datei), sonst JSON-Datei als Rückfall.
 * Ein Datensatz je (datum, feld): z.B. ("2026-09-06","vital.hrv") -> "41".
 * Der Dateiname ist aus dem geheimen Schlüssel abgeleitet (nicht erratbar).
 */

function vitara_db_path($ext) {
    $cfg = vitara_config();
    $suffix = substr(hash('sha256', 'vitara-db|' . (isset($cfg['client_secret']) ? $cfg['client_secret'] : 'x')), 0, 20);
    return __DIR__ . '/health-db-' . $suffix . '.' . $ext;
}

/** Liefert die SQLite-Verbindung oder null (dann JSON-Fallback). */
function vitara_db() {
    static $pdo = null, $tried = false;
    if ($tried) return $pdo;
    $tried = true;
    if (class_exists('PDO') && in_array('sqlite', PDO::getAvailableDrivers(), true)) {
        try {
            $pdo = new PDO('sqlite:' . vitara_db_path('sqlite'));
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec('CREATE TABLE IF NOT EXISTS werte (datum TEXT NOT NULL, feld TEXT NOT NULL, wert TEXT, PRIMARY KEY(datum, feld))');
        } catch (Exception $e) { $pdo = null; }
    }
    return $pdo;
}

function vitara_db_kind() { return vitara_db() ? 'sqlite' : 'json'; }

/** Einen Wert speichern (überschreibt vorhandenen Tageswert). */
function vitara_store($datum, $feld, $wert) {
    if ($wert === null || $datum === null || $datum === '') return;
    $db = vitara_db();
    if ($db) {
        $s = $db->prepare('INSERT INTO werte(datum, feld, wert) VALUES(?,?,?) ON CONFLICT(datum, feld) DO UPDATE SET wert = excluded.wert');
        try { $s->execute(array($datum, $feld, (string)$wert)); } catch (Exception $e) {}
        return;
    }
    // JSON-Fallback
    $p = vitara_db_path('json');
    $all = array();
    if (file_exists($p)) { $all = json_decode(file_get_contents($p), true); if (!is_array($all)) $all = array(); }
    if (!isset($all[$feld])) $all[$feld] = array();
    $all[$feld][$datum] = (string)$wert;
    @file_put_contents($p, json_encode($all));
}

/** Kompletter Speicher als { feld: { datum: wert } } – für die geräteübergreifende Spiegelung. */
function vitara_history_all() {
    $out = array();
    $db = vitara_db();
    if ($db) {
        try {
            $s = $db->query('SELECT datum, feld, wert FROM werte');
            if ($s) foreach ($s as $row) {
                if (!isset($out[$row['feld']])) $out[$row['feld']] = array();
                $out[$row['feld']][$row['datum']] = $row['wert'];
            }
        } catch (Exception $e) {}
        return $out;
    }
    $p = vitara_db_path('json');
    if (file_exists($p)) { $all = json_decode(file_get_contents($p), true); if (is_array($all)) $out = $all; }
    return $out;
}

/** Verlauf eines Feldes als { datum: wert } (neueste zuerst begrenzt auf $days). */
function vitara_history($feld, $days) {
    $days = max(1, min(1000, intval($days)));
    $out = array();
    $db = vitara_db();
    if ($db) {
        $s = $db->prepare('SELECT datum, wert FROM werte WHERE feld = ? ORDER BY datum DESC LIMIT ' . $days);
        try { $s->execute(array($feld)); foreach ($s as $row) { $out[$row['datum']] = $row['wert']; } } catch (Exception $e) {}
        return $out;
    }
    $p = vitara_db_path('json');
    if (file_exists($p)) { $all = json_decode(file_get_contents($p), true); if (isset($all[$feld]) && is_array($all[$feld])) $out = $all[$feld]; }
    return $out;
}
