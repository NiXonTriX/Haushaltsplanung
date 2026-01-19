<?php
declare(strict_types=1);

require_once __DIR__ . '/db/db.php';

try {
    $pdo = DB::getInstance()->getConnection();
    $pdo->query('SELECT 1');
    echo '✅ Datenbank erreichbar & SQL ausführbar';
} catch (Throwable $e) {
    http_response_code(500);
    echo '❌ Datenbankproblem: ' . htmlspecialchars($e->getMessage());
}
