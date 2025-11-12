<?php
// Endpoint JSON: cotation unitaire actions/ETF (Yahoo) ou crypto (CoinGecko)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../api/market_data.php';

$type = isset($_GET['type']) ? trim((string)$_GET['type']) : 'equity';
if ($type === 'crypto') {
    $id = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
    $symbol = isset($_GET['symbol']) ? trim((string)$_GET['symbol']) : '';
    if ($id === '') { echo json_encode(['ok' => false, 'error' => 'missing_id']); exit; }
    $res = crypto_price_aggregator($id, $symbol, 'eur');
    echo json_encode($res); exit;
}
$symbol = isset($_GET['symbol']) ? trim((string)$_GET['symbol']) : '';
if ($symbol === '') { echo json_encode(['ok' => false, 'error' => 'missing_symbol']); exit; }
$res = equity_quote_aggregator($symbol);
echo json_encode($res);
?>