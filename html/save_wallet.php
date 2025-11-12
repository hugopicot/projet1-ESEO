<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ./auth.php');
    exit;
}

$address = isset($_GET['address']) ? trim((string)$_GET['address']) : '';
if ($address === '' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = json_decode((string)file_get_contents('php://input'), true);
    $address = trim((string)($json['address'] ?? ''));
}

$isHex = preg_match('/^0x[a-fA-F0-9]{40}$/', $address) === 1;
if (!$isHex) {
    $_SESSION['error'] = "Adresse EVM invalide.";
    header('Location: ./connect_wallet.php');
    exit;
}

try { $pdo->exec("ALTER TABLE users ADD COLUMN wallet_address VARCHAR(64) NOT NULL DEFAULT ''"); } catch (Throwable $e) {}

try {
    $stmt = $pdo->prepare("UPDATE users SET wallet_address = ? WHERE id = ?");
    $stmt->execute([$address, $_SESSION['user_id']]);
    $_SESSION['wallet_address'] = $address;
    $_SESSION['success'] = 'Wallet connecté avec succès.';
} catch (Throwable $e) {
    $_SESSION['error'] = "Impossible d'enregistrer l'adresse.";
    header('Location: ./connect_wallet.php');
    exit;
}

$chain = 'eth';
header('Location: ./moralis_demo.php?address=' . urlencode($address) . '&chain=' . urlencode($chain));
exit;
