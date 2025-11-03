<?php
global $pdo;
session_start();
require_once '../config/config.php'; // adapte le chemin selon ton dossier

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Vérifie que tous les champs sont remplis
    if (empty($nom) || empty($email) || empty($message)) {
        echo "Veuillez remplir tous les champs.";
        exit;
    }

    // Vérifie le format de l'email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse email invalide.";
        exit;
    }

    // Prépare et exécute l’insertion
    $sql = "INSERT INTO messages (nom, email, message) VALUES (:nom, :email, :message)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':email' => $email,
        ':message' => $message
    ]);

    echo "Message envoyé avec succès.";
}

header("Status: 301 Moved Permanently", false, 301);
header("Location: http://projet1/index.php");
exit;


?>
