<?php
global $pdo;
session_start();
require_once '../config/config.php';

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth.php");
    exit;
}

$id = $_SESSION['user_id'];
$username = trim($_POST['username'] ?? '');
$email_user = filter_var(trim($_POST['email_user'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = trim($_POST['password'] ?? '');
$country = trim($_POST['country'] ?? '');

// Vérifications basiques
if (!$username || !$email_user) {
    $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
    header("Location: ./profil.php");
    exit;
}

try {
    // Vérifie si un autre utilisateur a déjà cet email
    $check = $pdo->prepare("SELECT id FROM users WHERE email_user = ? AND id != ?");
    $check->execute([$email_user, $id]);
    if ($check->rowCount() > 0) {
        $_SESSION['error'] = "Cet e-mail est déjà utilisé par un autre compte.";
        header("Location: ./profil.php");
        exit;
    }

    // Mise à jour
    if (empty($password)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email_user = ?, country = ? WHERE id = ?");
        $stmt->execute([$username, $email_user, $country, $id]);
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email_user = ?, password = ?, country = ? WHERE id = ?");
        $stmt->execute([$username, $email_user, $hashed, $country, $id]);
    }

    // Mise à jour de la session
    $_SESSION['username'] = $username;
    $_SESSION['success'] = "Profil mis à jour avec succès.";

} catch (PDOException $e) {
    error_log("Erreur SQL dans update_profile.php : " . $e->getMessage());
    $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du profil.";
}

header("Location: ./profil.php");
exit;
