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
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = trim($_POST['password'] ?? '');
$country = trim($_POST['country'] ?? '');

// Vérifications basiques
if (!$username || !$email) {
    $_SESSION['error'] = "Veuillez remplir tous les champs obligatoires.";
    header("Location: ./profil.php");
    exit;
}

try {
    // Vérifie si un autre utilisateur a déjà cet e-mail
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->execute([$email, $id]);
    if ($check->rowCount() > 0) {
        $_SESSION['error'] = "Cet e-mail est déjà utilisé par un autre compte.";
        header("Location: ./profil.php");
        exit;
    }

    // Prépare la requête selon que le mot de passe a été modifié ou non
    if (empty($password)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, country = ? WHERE id = ?");
        $stmt->execute([$username, $email, $country, $id]);
    } else {
        // Hachage sécurisé du nouveau mot de passe
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password = ?, country = ? WHERE id = ?");
        $stmt->execute([$username, $email, $hashed, $country, $id]);
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
