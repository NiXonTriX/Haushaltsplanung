<?php
declare(strict_types=1);

require_once __DIR__ . '/db/bookings.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $repo = new Bookings();
    $data = $repo->selectAll();

    echo json_encode($data, JSON_THROW_ON_ERROR);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage()
    ]);
}
