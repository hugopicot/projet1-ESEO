<?php
global $pdo;
session_start();
require_once '../config/config.php'; // adapte le chemin selon ton dossier

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    if (empty($nom) || empty($email) || empty($message)) {
        $_SESSION['error'] = "Veuillez remplir tous les champs.";
        header("Location: ../index.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Adresse email invalide.";
        header("Location: ../index.php");
        exit;
    }

    $sql = "INSERT INTO messages (nom, email, message) VALUES (:nom, :email, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':message' => $message
    ]);

    $_SESSION['success'] = "Message envoyé avec succès.";
    header("Location: ../index.php");
    exit;
}

header("Location: ../index.php");
exit;

?>
