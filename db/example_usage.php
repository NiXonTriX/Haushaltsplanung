<?php
// Beispiel: einfache Verbindungsprüfung und Abfrage
require_once __DIR__ . '/db.php';

// Option A: Konfiguration per Konstanten (optional)
// define('DB_HOST', '127.0.0.1');
// define('DB_NAME', 'haushalt');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_CHARSET', 'utf8mb4');

try {
    // Option B: Konfiguration beim ersten Aufruf übergeben
    // $db = DB::getInstance(['host'=>'127.0.0.1','dbname'=>'haushalt','user'=>'root','pass'=>'','charset'=>'utf8mb4'])->getConnection();

    $db = DB::getInstance()->getConnection();
    $stmt = $db->query('SELECT 1 AS ok');
    $row = $stmt->fetch();
    echo "Verbindung erfolgreich: " . ($row['ok'] ? 'OK' : 'FAIL') . PHP_EOL;
} catch (Exception $e) {
    echo 'Fehler: ' . $e->getMessage() . PHP_EOL;
}
