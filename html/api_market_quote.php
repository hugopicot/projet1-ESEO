<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../api/market_data.php';

$type = isset($_GET['type']) ? trim((string)$_GET['type']) : 'crypto';
if ($type !== 'crypto') {
    echo json_encode(['ok' => false, 'error' => 'unsupported_type']);
    exit;
}
    $id = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
    $symbol = isset($_GET['symbol']) ? trim((string)$_GET['symbol']) : '';
    if ($id === '') { echo json_encode(['ok' => false, 'error' => 'missing_id']); exit; }
    $res = crypto_price_aggregator($id, $symbol, 'eur');
    echo json_encode($res); exit;
}
?>
