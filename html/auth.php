<?php
global $pdo;
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once '../config/config.php'; // s'assure que $pdo est défini

if (isset($_POST['login'])) {
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email) {
        $_SESSION['error'] = "Email invalide.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email_user = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'Admin') {
                header("Location: ./admin_panel.php");
                exit;
            } else {
                header("Location: ./app.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Identifiants incorrects.";
        }
    }
}



// --- REGISTER ---
if (isset($_POST['register'])) {
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!$email) {
        $_SESSION['error'] = "Email invalide.";
    } elseif (strlen($password) < 8) {
        $_SESSION['error'] = "Mot de passe trop court (>= 8).";
    } else {
        // Vérifier doublon par email
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);

        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "Email déjà utilisé.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            try {
                $stmt->execute([$name, $email, $hashed]);
                $_SESSION['success'] = "Inscription réussie. Vous pouvez maintenant vous connecter.";
                header("Location: auth.php");
                exit;
            } catch (PDOException $e) {
                // en dev tu peux logger l'erreur
                error_log("PDO insert error: " . $e->getMessage());
                $_SESSION['error'] = "Erreur serveur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova - Espace Authentification</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>
<main class="auth-layout">
    <section class="auth-content">
        <div class="auth-intro">
            <img src="../asset/img/logo.png" alt="Nova" class="auth-logo">
            <h1>Nova Platform</h1>
            <p>Centralisez vos finances, automatisez vos flux et collaborez avec votre equipe depuis un environnement securise.</p>
            <ul>
                <li>Suivi des transactions en temps reel</li>
                <li>Rappels intelligents et alertes securisees</li>
                <li>Tableaux de bord partageables avec votre equipe</li>
            </ul>
        </div>

        <div class="auth-card" role="region" aria-labelledby="auth-tabs">
            <nav class="auth-tabs" id="auth-tabs" aria-label="Choix du formulaire">
                <button type="button" class="auth-tab is-active" data-auth-tab="login">Connexion</button>
                <button type="button" class="auth-tab" data-auth-tab="register">Inscription</button>
                <button type="button" class="auth-tab" data-auth-tab="reset">Mot de passe</button>
            </nav>
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='error'>{$_SESSION['error']}</p>";
                unset($_SESSION['error']);
            }
            ?>
            <form action="auth.php" method="post" class="auth-form is-active" data-auth-panel="login" novalidate>
                <div class="auth-form-header">
                    <h2>Connectez-vous a votre compte</h2>
                    <p>Entrez vos identifiants pour acceder a votre espace Nova.</p>
                </div>
                <div class="auth-field">
                    <label for="login-email">Email</label>
                    <input type="email" id="login-email" name="email" autocomplete="email" required>
                </div>
                <div class="auth-field">
                    <label for="login-password">Mot de passe</label>
                    <input type="password" id="login-password" name="password" autocomplete="current-password" required>
                </div>
                <div class="auth-form-row">
                    <label class="auth-checkbox">
                        <input type="checkbox" name="remember">
                        <span>Se souvenir de moi</span>
                    </label>
                    <button type="button" class="auth-link" data-switch-to="reset">Mot de passe oublie ?</button>
                </div>
                <button type="submit" name="login" class="auth-button">Se connecter</button>
                <p class="auth-footer-text">
                    Pas encore de compte ?
                    <button type="button" class="auth-link" data-switch-to="register">Creer un compte</button>
                </p>
            </form>
            <form action="auth.php" method="post" class="auth-form" data-auth-panel="register" novalidate>
                <div class="auth-form-header">
                    <h2>Creer votre compte</h2>
                    <p>Rejoignez Nova en quelques secondes et commencez a piloter vos finances.</p>
                </div>
                <div class="auth-field">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="email" autocomplete="email" required>
                </div>
                <div class="auth-field">
                    <label for="register-name">Nom complet</label>
                    <input type="text" id="register-name" name="name" autocomplete="name" required>
                </div>
                <div class="auth-field">
                    <label for="register-password">Mot de passe</label>
                    <input type="password" id="register-password" name="password" autocomplete="new-password" required>
                </div>
                <label class="auth-checkbox">
                    <input type="checkbox" name="updates">
                    <span>Recevoir les actualites produit et les alertes de securite.</span>
                </label>
                <button type="submit" name="register" class="auth-button">Créer un compte</button>
                <p class="auth-footer-text">
                    Deja membre ?
                    <button type="button" class="auth-link" data-switch-to="login">Se connecter</button>
                </p>
            </form>

            <form action="auth.php" method="post" class="auth-form" data-auth-panel="reset" novalidate>
                <div class="auth-form-header">
                    <h2>Reinitialiser votre mot de passe</h2>
                    <p>Saisissez votre email. Nous vous enverrons un lien de reinitialisation.</p>
                </div>
                <div class="auth-field">
                    <label for="reset-email">Email</label>
                    <input type="email" id="reset-email" name="email" autocomplete="email" required>
                </div>
                <button type="submit" class="auth-button">Envoyer le lien</button>
                <p class="auth-footer-text">
                    Besoin d'un compte ?
                    <button type="button" class="auth-link" data-switch-to="register">Creer un compte</button>
                </p>
                <p class="auth-footer-text">
                    Vous souvenez-vous de votre mot de passe ?
                    <button type="button" class="auth-link" data-switch-to="login">Retour a la connexion</button>
                </p>
            </form>
        </div>
    </section>
</main>

<script src="../js/auth.js"></script>
</body>
</html>
