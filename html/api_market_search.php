<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../api/market_data.php';

$q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
$type = isset($_GET['type']) ? trim((string)$_GET['type']) : 'crypto';
if ($q === '') { echo json_encode(['ok' => false, 'error' => 'empty_query']); exit; }

if ($type !== 'crypto') { echo json_encode(['ok' => false, 'error' => 'unsupported_type']); exit; }
$res = coingecko_search($q);
echo json_encode($res);
?>
