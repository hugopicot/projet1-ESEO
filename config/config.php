<?php
$host = 'localhost';
$dbname = 'projet_site1'; // évite les espaces dans le nom de la base
$user = 'root';
$pass = ''; // Mets ton mot de passe si nécessaire

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // gestion des erreurs
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // fetch associatif par défaut
        PDO::ATTR_EMULATE_PREPARES => false, // sécurité pour les requêtes préparées
    ]);
} catch (PDOException $e) {
    // Message d'erreur simplifié pour éviter de révéler des infos sensibles en prod
    die("Erreur de connexion à la base de données. Détails : " . htmlspecialchars($e->getMessage()));
}
?>
