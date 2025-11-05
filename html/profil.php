<?php
global $pdo;
session_start();
require_once '../config/config.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth.php");
    exit;
}

// Récupère les infos depuis la BDD
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    $_SESSION['error'] = "Utilisateur introuvable.";
    header("Location: ./app.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mon Profil - NOVA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="profile-page d-flex align-items-center justify-content-center">
<div class="container-fluid">
    <div class="row flex-nowrap">

        <!-- SIDEBAR -->
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-3 text-white min-vh-100">
                <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
                    <span class="fs-4 fw-bold text-lime">👤 Mon Profil</span>
                </a>

                <ul class="nav nav-pills flex-column mb-auto w-100" id="menu">
                    <li class="nav-item">
                        <a href="./profil.php" class="nav-link text-white active">
                            <i class="bi bi-person-circle me-2"></i> Profil
                        </a>
                    </li>
                    <li>
                        <a href="./settings.php" class="nav-link text-white">
                            <i class="bi bi-gear me-2"></i> Paramètres
                        </a>
                    </li>
                </ul>

                <hr class="text-secondary w-100">
                <div class="mt-auto mb-3">
                    <a href="app.php" class="btn btn-cancel w-100">⬅ Retour</a>
                </div>
            </div>
        </div>

        <!-- CONTENU PRINCIPAL (Met ton formulaire ici) -->
        <div class="col py-5 px-4 main-content">
            <!-- Ton contenu Profil -->
            <div class="profile-card p-4">
                <h3 class="text-white mb-4 fw-bold text-center">Mon Profil</h3>

                <!-- Messages de confirmation ou d'erreur -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
                <?php elseif (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
                <?php endif; ?>

                <!-- Formulaire -->
                <form action="update_profile.php" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label text-secondary">Nom complet</label>
                        <input type="text" class="form-control input-dark" id="name" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label text-secondary">Adresse e-mail</label>
                        <input type="email" class="form-control input-dark" id="email" name="email_user" value="<?= htmlspecialchars($user['email_user']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label text-secondary">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                        <input type="password" class="form-control input-dark" id="password" name="password" placeholder="••••••••">
                    </div>

                    <div class="mb-3">
                        <label for="country" class="form-label text-secondary">Pays</label>
                        <select class="form-select input-dark" id="country" name="country">
                            <?php
                            $countries = ["France", "Belgique", "Suisse", "Canada", "Autre"];
                            foreach ($countries as $country) {
                                $selected = ($user['country'] === $country) ? 'selected' : '';
                                echo "<option value=\"$country\" $selected>$country</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary">Compte créé le</label>
                        <p class="text-white fw-semibold mb-0"><?= date('d/m/Y', strtotime($user['created_at'])); ?></p>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-save">Enregistrer les modifications</button>
                        <a href="app.php" class="btn btn-cancel">Retour</a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>



</body>
</html>