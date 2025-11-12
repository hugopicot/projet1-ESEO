<?php
// Configuration globale du projet

// Overrides locaux optionnels (non versionnés) si présents
// Vous pouvez créer config/local.php pour définir des constantes comme POWENS_CLIENT_SECRET
// sans dépendre des variables d’environnement ou des services Windows.
@include __DIR__ . '/local.php';

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

// Domaine de base (utilisé pour les URLs de retour Stripe/Powens)
// 1) Priorité à la variable d'environnement APP_DOMAIN si définie
// 2) Sinon, détection automatique du schéma et de l'hôte (incluant le port)
if (!defined('APP_DOMAIN')) {
    $envAppDomain = getenv('APP_DOMAIN') ?: '';
    if ($envAppDomain !== '') {
        // Normalise en retirant un éventuel slash final
        $envAppDomain = rtrim($envAppDomain, '/');
        define('APP_DOMAIN', $envAppDomain);
    } else {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? '';
        if ($host !== '') {
            define('APP_DOMAIN', $scheme . '://' . $host);
        } else {
            // Fallback si exécuté en contexte sans serveur HTTP (CLI)
            define('APP_DOMAIN', 'http://localhost');
        }
    }
}

// Base de redirection Powens (permet d'éviter les erreurs d'invalidation redirect_uri)
// Priorité à la variable d'environnement POWENS_REDIRECT_BASE, sinon APP_DOMAIN
if (!defined('POWENS_REDIRECT_BASE')) {
    // Support des variables de tunnel communes (ngrok/xgrok)
    $powensBase = getenv('POWENS_REDIRECT_BASE') ?: '';
    $ngrokUrl   = getenv('NGROK_URL') ?: getenv('NGROK_HOST') ?: '';
    $xgrokUrl   = getenv('XGROK_URL') ?: getenv('XGROK_HOST') ?: '';
    if ($powensBase !== '') {
        define('POWENS_REDIRECT_BASE', rtrim($powensBase, '/'));
    } elseif ($ngrokUrl !== '') {
        define('POWENS_REDIRECT_BASE', rtrim($ngrokUrl, '/'));
    } elseif ($xgrokUrl !== '') {
        define('POWENS_REDIRECT_BASE', rtrim($xgrokUrl, '/'));
    } else {
        // Fallback intelligent: en local, forcer la base sur localhost:8000
        $appHost = parse_url(APP_DOMAIN, PHP_URL_HOST) ?: '';
        if ($appHost === 'localhost' || $appHost === '127.0.0.1') {
            define('POWENS_REDIRECT_BASE', 'http://localhost:8000');
        } else {
            define('POWENS_REDIRECT_BASE', APP_DOMAIN);
        }
    }
}

// Page de redirection après connexion Powens (chemin ou URL complète)
// Par défaut: renvoie vers la page patrimoine
if (!defined('POWENS_POST_REDIRECT')) {
    $postRedirect = getenv('POWENS_POST_REDIRECT') ?: '/html/patrimoine.php';
    // Normalise les doubles slash accidentels
    if ($postRedirect !== '' && $postRedirect[0] !== '/') {
        // Autorise URLs absolues; sinon force un chemin relatif racine
        if (strpos($postRedirect, 'http') !== 0) {
            $postRedirect = '/' . ltrim($postRedirect, '/');
        }
    }
    define('POWENS_POST_REDIRECT', $postRedirect);
}

// Identifiants Powens (via env de préférence)
if (!defined('POWENS_CLIENT_ID')) {
    define('POWENS_CLIENT_ID', getenv('POWENS_CLIENT_ID') ?: '9941084');
}
if (!defined('POWENS_CLIENT_SECRET')) {
    // Doit être défini dans l'environnement pour la synchro réelle
    define('POWENS_CLIENT_SECRET', getenv('POWENS_CLIENT_SECRET') ?: '');
}
if (!defined('POWENS_DOMAIN')) {
    define('POWENS_DOMAIN', getenv('POWENS_DOMAIN') ?: 'nova-sandbox.biapi.pro');
}
if (!defined('POWENS_API_BASE')) {
    // Par défaut API publique; vous pouvez override via env POWENS_API_BASE
    define('POWENS_API_BASE', getenv('POWENS_API_BASE') ?: 'https://api.powens.com');
}

// Connexion base de données
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

// Moralis API configuration (Web3 data)
if (!defined('MORALIS_API_BASE')) {
    $moralisBase = getenv('MORALIS_API_BASE') ?: 'https://deep-index.moralis.io/api/v2.2';
    define('MORALIS_API_BASE', $moralisBase);
}
if (!defined('MORALIS_API_KEY')) {
    // Définissez la variable d’environnement MORALIS_API_KEY en local/production
    define('MORALIS_API_KEY', getenv('MORALIS_API_KEY') ?: '');
}

// Remarque: Ce fichier ne doit produire aucune sortie HTML/texte.
// Pas de tag de fermeture PHP pour éviter toute sortie involontaire.