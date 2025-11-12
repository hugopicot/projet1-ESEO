<?php

@include __DIR__ . '/local.php';

$envStripeSecret = getenv('STRIPE_SECRET_KEY') ?: '';
$envStripePublishable = getenv('STRIPE_PUBLISHABLE_KEY') ?: '';

if (!defined('STRIPE_SECRET_KEY')) {
    define('STRIPE_SECRET_KEY', $envStripeSecret ?: 'sk_test_51SPOJp7eOcswKrFeMA4bLu97didfxphLLrHwBTsEyyRoP1kGYIzxpIN1Dqmc7ifVGfXJhpc4Po8rOMrJvlz3hWON00DNd9xx5p');
}
if (!defined('STRIPE_PUBLISHABLE_KEY')) {
    define('STRIPE_PUBLISHABLE_KEY', $envStripePublishable ?: 'pk_test_51SPOJp7eOcswKrFerYc4mS1mSppZ4ZSrEnFmx9aYAO03DQ6Jdpv7nv5DtKguo4IJitZTcHxhOcAi1xdXvlj1mSM700tkiYEOpL');
}

if (!defined('STRIPE_WEBHOOK_SECRET')) {
    define('STRIPE_WEBHOOK_SECRET', getenv('STRIPE_WEBHOOK_SECRET') ?: '');
}

if (!defined('APP_DOMAIN')) {
    $envAppDomain = getenv('APP_DOMAIN') ?: '';
    if ($envAppDomain !== '') {
        $envAppDomain = rtrim($envAppDomain, '/');
        define('APP_DOMAIN', $envAppDomain);
    } else {
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? '';
        if ($host !== '') {
            define('APP_DOMAIN', $scheme . '://' . $host);
        } else {
            define('APP_DOMAIN', 'http://localhost');
        }
    }
}

if (!defined('POWENS_REDIRECT_BASE')) {
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
        $appHost = parse_url(APP_DOMAIN, PHP_URL_HOST) ?: '';
        if ($appHost === 'localhost' || $appHost === '127.0.0.1') {
            define('POWENS_REDIRECT_BASE', 'http://localhost:8000');
        } else {
            define('POWENS_REDIRECT_BASE', APP_DOMAIN);
        }
    }
}

if (!defined('POWENS_POST_REDIRECT')) {
    $postRedirect = getenv('POWENS_POST_REDIRECT') ?: '/html/patrimoine.php';
    if ($postRedirect !== '' && $postRedirect[0] !== '/') {
        if (strpos($postRedirect, 'http') !== 0) {
            $postRedirect = '/' . ltrim($postRedirect, '/');
        }
    }
    define('POWENS_POST_REDIRECT', $postRedirect);
}

if (!defined('POWENS_CLIENT_ID')) {
    define('POWENS_CLIENT_ID', getenv('POWENS_CLIENT_ID') ?: '9941084');
}
if (!defined('POWENS_CLIENT_SECRET')) {
    define('POWENS_CLIENT_SECRET', getenv('POWENS_CLIENT_SECRET') ?: '');
}
if (!defined('POWENS_DOMAIN')) {
    define('POWENS_DOMAIN', getenv('POWENS_DOMAIN') ?: 'nova-sandbox.biapi.pro');
}
if (!defined('POWENS_API_BASE')) {
    define('POWENS_API_BASE', getenv('POWENS_API_BASE') ?: 'https://api.powens.com');
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
    die("Erreur de connexion à la base de données. Détails : " . htmlspecialchars($e->getMessage()));
}

if (!defined('MORALIS_API_BASE')) {
    $moralisBase = getenv('MORALIS_API_BASE') ?: 'https://deep-index.moralis.io/api/v2.2';
    define('MORALIS_API_BASE', $moralisBase);
}
if (!defined('MORALIS_API_KEY')) {
    define('MORALIS_API_KEY', getenv('MORALIS_API_KEY') ?: '');
}

