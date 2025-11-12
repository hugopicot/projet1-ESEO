<?php
// Endpoint JSON: recherche actions/ETF (Yahoo) et crypto (CoinGecko)
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../api/market_data.php';

$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$type = isset($_GET['type']) ? trim((string)$_GET['type']) : 'equity'; // 'equity' | 'crypto'
if ($q === '') { echo json_encode(['ok' => false, 'error' => 'empty_query']); exit; }

if ($type === 'crypto') {
    $res = coingecko_search($q);
    echo json_encode($res); exit;
}

// Support de paramètres région/langue pour améliorer la couverture des recherches (ex: Tesla)
$lang = isset($_GET['lang']) ? trim((string)$_GET['lang']) : 'fr';
$region = isset($_GET['region']) ? trim((string)$_GET['region']) : 'FR';
$res = yahoo_search($q, $lang, $region);
echo json_encode($res);
?>