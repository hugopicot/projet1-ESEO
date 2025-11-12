<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) { echo json_encode(['ok' => false, 'error' => 'unauthorized']); exit; }
$userId = (int)$_SESSION['user_id'];

$range = isset($_GET['range']) ? strtoupper(trim((string)$_GET['range'])) : '1M';
$valid = ['1D','1W','1M','1Y','YTD','ALL'];
if (!in_array($range, $valid, true)) { $range = '1M'; }

$assetsParam = isset($_GET['assets']) ? trim((string)$_GET['assets']) : '';
$assetsFilter = [];
if ($assetsParam !== '') { $assetsFilter = array_values(array_filter(array_map('trim', explode(',', $assetsParam)), fn($s) => $s !== '')); }

$nowTs = time();
switch ($range) {
    case '1D':
        $cutoffTs = $nowTs - 86400;
        break;
    case '1W':
        $cutoffTs = $nowTs - (7*86400);
        break;
    case '1M':
        $cutoffTs = $nowTs - (30*86400);
        break;
    case '1Y':
        $cutoffTs = $nowTs - (365*86400);
        break;
    case 'YTD':
        $cutoffTs = mktime(0, 0, 0, 1, 1, (int)date('Y'));
        break;
    case 'ALL':
    default:
        $cutoffTs = 0;
        break;
}
$cutoff = $cutoffTs > 0 ? date('Y-m-d H:i:s', $cutoffTs) : null;

try {
    $sqlAssets = "SELECT external_id, categorie, libelle FROM compte WHERE user_id = ? AND source='manual' AND status='active' AND categorie IN ('crypto','actions_fonds')";
    $stAssets = $pdo->prepare($sqlAssets);
    $stAssets->execute([$userId]);
    $assetsRows = $stAssets->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $assetIds = [];
    foreach ($assetsRows as $r) {
        $eid = (string)$r['external_id'];
        if ($eid === '') { continue; }
        if (!empty($assetsFilter) && !in_array($eid, $assetsFilter, true)) { continue; }
        $assetIds[$eid] = [
            'external_id' => $eid,
            'name' => (string)($r['libelle'] ?? $eid),
            'categorie' => (string)$r['categorie'],
        ];
    }

    if (empty($assetIds)) { echo json_encode(['ok' => true, 'assets' => [], 'total' => [], 'range' => $range]); exit; }

    $placeholders = implode(',', array_fill(0, count($assetIds), '?'));
    $args = [$userId];
    foreach (array_keys($assetIds) as $eid) { $args[] = $eid; }
    $q = "SELECT external_id, value_eur, created_at FROM valuations_history WHERE user_id = ? AND external_id IN ($placeholders)";
    if ($cutoff !== null) { $q .= " AND created_at >= ?"; $args[] = $cutoff; }
    $q .= " ORDER BY created_at ASC";
    $st = $pdo->prepare($q);
    $st->execute($args);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

    $byAsset = [];
    foreach ($rows as $r) {
        $eid = (string)$r['external_id'];
        $val = (float)$r['value_eur'];
        $ts = strtotime((string)$r['created_at']);
        $byAsset[$eid][] = ['time' => $ts, 'value' => $val];
    }

    $totalMap = [];
    foreach ($byAsset as $eid => $series) {
        foreach ($series as $pt) { $totalMap[$pt['time']] = ($totalMap[$pt['time']] ?? 0) + $pt['value']; }
    }
    ksort($totalMap);
    $totalSeries = [];
    foreach ($totalMap as $ts => $sum) { $totalSeries[] = ['time' => $ts, 'value' => $sum]; }

    foreach ($assetIds as $eid => $meta) {
        if (empty($byAsset[$eid])) {
            try {
                $cur = $pdo->prepare("SELECT balance_eur FROM compte WHERE user_id = ? AND external_id = ? AND source='manual' AND status='active' LIMIT 1");
                $cur->execute([$userId, $eid]);
                $val = (float)($cur->fetchColumn() ?: 0);
                $byAsset[$eid] = [['time' => $nowTs, 'value' => $val]];
            } catch (Throwable $e2) { $byAsset[$eid] = [['time' => $nowTs, 'value' => 0]]; }
        }
    }

    $assetsOut = [];
    foreach ($assetIds as $eid => $meta) {
        $assetsOut[] = [
            'external_id' => $eid,
            'name' => $meta['name'],
            'categorie' => $meta['categorie'],
            'points' => $byAsset[$eid],
        ];
    }

    echo json_encode(['ok' => true, 'assets' => $assetsOut, 'total' => $totalSeries, 'range' => $range]);
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'error' => 'server_error']);
}
?>
