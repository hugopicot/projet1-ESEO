<?php
session_start();
require_once __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ./auth.php');
    exit;
}

// Support POST ou GET
$input = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = (string)file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) { $input = $json; }
    else { $input = $_POST; }
} else { $input = $_GET; }

$category = trim((string)($input['category'] ?? ''));
$external = trim((string)($input['external_id'] ?? ''));

if ($category !== 'crypto' || $external === '') {
    $_SESSION['error'] = "Requête de suppression invalide.";
    header('Location: ./patrimoine.php');
    exit;
}

try {
    $pdo->beginTransaction();
    // Supprime la position manuelle
    $stmt = $pdo->prepare("DELETE FROM positions_manual WHERE user_id = ? AND categorie = ? AND external_id = ?");
    $stmt->execute([$_SESSION['user_id'], $category, $external]);

    // Marque le compte comme fermé (on garde l’historique)
    $upd = $pdo->prepare("UPDATE compte SET status = 'closed', updated_at = NOW() WHERE user_id = ? AND source = 'manual' AND categorie = ? AND external_id = ?");
    $upd->execute([$_SESSION['user_id'], $category, $external]);
    $pdo->commit();

    $_SESSION['success'] = 'Actif supprimé.';
} catch (Throwable $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); }
    $_SESSION['error'] = "Échec de la suppression de l'actif.";
}

header('Location: ./patrimoine.php');
exit;
?>