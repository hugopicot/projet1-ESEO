<?php
// Configuration globale du projet

// Lecture des clés Stripe en priorité via variables d'environnement
// Définissez STRIPE_SECRET_KEY et STRIPE_PUBLISHABLE_KEY dans votre environnement si possible.
$envStripeSecret = getenv('STRIPE_SECRET_KEY') ?: '';
$envStripePublishable = getenv('STRIPE_PUBLISHABLE_KEY') ?: '';

// Fallback: clés de test à compléter (NE JAMAIS mettre des clés live ici)
if (!defined('STRIPE_SECRET_KEY')) {
    // Fallback local (test) si variable d’environnement non définie
    define('STRIPE_SECRET_KEY', $envStripeSecret ?: 'sk_test_51SPOJp7eOcswKrFeMA4bLu97didfxphLLrHwBTsEyyRoP1kGYIzxpIN1Dqmc7ifVGfXJhpc4Po8rOMrJvlz3hWON00DNd9xx5p');
}
if (!defined('STRIPE_PUBLISHABLE_KEY')) {
    // Fallback local (test) si variable d’environnement non définie
    define('STRIPE_PUBLISHABLE_KEY', $envStripePublishable ?: 'pk_test_51SPOJp7eOcswKrFerYc4mS1mSppZ4ZSrEnFmx9aYAO03DQ6Jdpv7nv5DtKguo4IJitZTcHxhOcAi1xdXvlj1mSM700tkiYEOpL');
}

// Secret webhook (facultatif en local). Vous pouvez le définir via env STRIPE_WEBHOOK_SECRET.
if (!defined('STRIPE_WEBHOOK_SECRET')) {
    define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: '');
}

// Domaine local (utilisé pour les URLs de retour Stripe)
if (!defined('APP_DOMAIN')) {
    define('APP_DOMAIN', 'http://localhost:8000');
}
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
