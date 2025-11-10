<?php
global $pdo;
session_start();
require_once '../config/config.php';

// Vérifie si l'utilisateur est admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/auth.php");
    exit;
}

$stmt = $pdo->prepare("SELECT role FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'Admin') {
    header("Location: ../html/app.php");
    exit;
}

// Statistiques simples
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStarter = $pdo->query("SELECT COUNT(*) FROM users WHERE subscription_plan = 'starter'")->fetchColumn();
$totalPro = $pdo->query("SELECT COUNT(*) FROM users WHERE subscription_plan = 'pro'")->fetchColumn();

// Liste des utilisateurs
$users = $pdo->query("SELECT id, username, email_user, role, subscription_plan FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - NOVA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body { background: #0d0d0d; color: white; }
        .sidebar { width: 250px; background: #111; min-height: 100vh; padding: 20px; }
        .sidebar a { color: #bbb; display: block; margin-bottom: 10px; }
        .sidebar a.active, .sidebar a:hover { color: #8CFF2E; }
        .main { padding: 20px; flex: 1; }
        .card-stat { background: #161616; border-radius: 12px; padding: 20px; }
        .table-dark { background: #111; }
        .btn-sm { padding: 4px 8px; }
    </style>
</head>
<body>

<div class="d-flex">

    <!-- Sidebar -->
    <div class="sidebar">
        <h4 class="text-white mb-4">⚙️ NOVA ADMIN</h4>
        <a href="panel_admin.php" class="active">Dashboard</a>
        <a href="settings.php">Paramètres</a>
        <a href="../index.php">Retour au site</a>
        <hr style="border-color:#333;">
        <p class="text-secondary small">Connecté en tant que : Admin</p>
    </div>

    <!-- Main -->
    <div class="main">

        <h2 class="mb-4 fw-bold">Tableau de bord</h2>

        <div class="row g-3 mb-4">
            <div class="col-md-4"><div class="card-stat">Total utilisateurs : <b><?= $totalUsers ?></b></div></div>
            <div class="col-md-4"><div class="card-stat">Starter : <b><?= $totalStarter ?></b></div></div>
            <div class="col-md-4"><div class="card-stat">Pro : <b><?= $totalPro ?></b></div></div>
        </div>

        <h4 class="fw-bold mt-4 mb-3">Gestion des utilisateurs</h4>

        <table class="table table-dark table-hover table-bordered align-middle text-center">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Rôle</th>
                <th>Abonnement</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= $u['username'] ?></td>
                    <td><?= $u['email_user'] ?></td>
                    <td><?= $u['role'] ?></td>
                    <td><?= $u['subscription_plan'] ?></td>
                    <td>
                        <a href="../config/update_role.php?id=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Changer rôle</a>
                        <a href="../config/delete_user.php?id=<?= $u['id'] ?>" onclick="return confirm('Supprimer ce compte ?')" class="btn btn-danger btn-sm">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

    </div>

</div>

</body>
</html>
