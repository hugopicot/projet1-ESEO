<?php
// Version simplifiée: construit l'URL Powens puis redirige, en stockant uniquement en session.
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Génère un identifiant d'état simple (pour suivi minimal)
$state = uniqid('st_', true);

// Paramètres d'entrée
$capabilities  = isset($_GET['connector_capabilities']) ? (string)$_GET['connector_capabilities'] : '';
$accountTypes  = isset($_GET['account_types']) ? (string)$_GET['account_types'] : '';
$staticLinkGet = isset($_GET['static_link']) ? (string)$_GET['static_link'] : '';
$returnToGet   = isset($_GET['return_to']) ? (string)$_GET['return_to'] : '';
$debug         = isset($_GET['debug']) ? (string)$_GET['debug'] : '';

// Détermine la page de retour (GET -> Referer -> défaut)
$returnTo = $returnToGet;
if ($returnTo === '') {
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '') {
        $path = parse_url($ref, PHP_URL_PATH);
        if (is_string($path) && $path !== '') { $returnTo = $path; }
    }
}
if ($returnTo === '') { $returnTo = '/html/add_assets.php'; }

// Stocke en session (pas de fichier JSON)
$_SESSION['powens_state'] = $state;
$_SESSION['powens_return_to'] = $returnTo;

// Constantes Powens
$baseUrl     = 'https://webview.powens.com/fr/connect';
$domainParam = 'nova-sandbox.biapi.pro';
$clientId    = '9941084';
$redirectUri = 'http://localhost:8000/html/powens_callback.php';

// Construit le lien de base
$staticLink = '';
if ($staticLinkGet !== '') {
    $staticLink = $staticLinkGet;
} else {
    $params = [
        'domain'       => $domainParam,
        'client_id'    => $clientId,
        'redirect_uri' => $redirectUri,
    ];
    if ($capabilities !== '') {
        $params['connector_capabilities'] = $capabilities;
        if (stripos($capabilities, 'bankwealth') !== false) { $params['usecase'] = 'wealth'; }
    }
    if ($accountTypes !== '') { $params['account_types'] = $accountTypes; }
    $staticLink = $baseUrl . '?' . http_build_query($params);
}

// Ajoute l'état
$target = $staticLink . (strpos($staticLink, '?') !== false ? '&' : '?') . 'state=' . urlencode($state);

// Mode debug: affiche la cible
if ($debug === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "target=\n" . $target . "\n";
    exit;
}

header('Location: ' . $target, true, 302);
exit;