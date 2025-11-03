<?php

global $pdo;
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan = $_POST['plan'] ?? 'none';
    if (!in_array($plan, ['starter', 'pro'])) {
        die("Plan invalide.");
    }

    // Simulation de paiement réussi
    $start = date('Y-m-d');
    $end = date('Y-m-d', strtotime('+1 month'));

    $stmt = $pdo->prepare("UPDATE users SET subscription_plan = ?, subscription_start = ?, subscription_end = ? WHERE id = ?");
    $stmt->execute([$plan, $start, $end, $_SESSION['user_id']]);

    $_SESSION['message'] = "Félicitations, votre abonnement $plan est actif jusqu’au $end !";
    header("Location: app.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Abonnement - Nova</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
<h1>Choisissez votre abonnement</h1>
<form method="POST">
    <button name="plan" value="starter">Plan Starter - 29€/mois</button>
    <button name="plan" value="pro">Plan Pro - 49€/mois</button>
</form>
</body>
</html>
