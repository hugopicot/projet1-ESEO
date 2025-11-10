<?php
// Endpoint de redirection vers le WebView Powens Connect.
// Construit l’URL en fonction des paramètres de catégorie et ajoute un state pour restaurer la session.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Générer un state pour rétablir l’utilisateur même si le cookie n’est pas renvoyé au callback
$userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
try {
    $state = bin2hex(random_bytes(16));
} catch (Throwable $e) {
    $state = uniqid('st_', true);
}

// Stocker le mapping state -> user_id (fichier JSON en local pour le MVP)
$stateFile = dirname(__DIR__) . '/bd/powens_states.json';
$states = [];
if (file_exists($stateFile)) {
    $raw = @file_get_contents($stateFile);
    $tmp = json_decode((string)$raw, true);
    if (is_array($tmp)) { $states = $tmp; }
}
$states[$state] = [
    'user_id'    => $userId,
    'created_at' => date('c'),
];
@file_put_contents($stateFile, json_encode($states, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Paramètres reçus pour construire l’URL
$capabilities   = isset($_GET['connector_capabilities']) ? (string)$_GET['connector_capabilities'] : '';
$accountTypes   = isset($_GET['account_types']) ? (string)$_GET['account_types'] : '';
$accountType    = isset($_GET['account_type']) ? (string)$_GET['account_type'] : '';
$staticLinkGet  = isset($_GET['static_link']) ? (string)$_GET['static_link'] : '';
$debugRequested = isset($_GET['debug']) ? (string)$_GET['debug'] : '';
$returnToGet    = isset($_GET['return_to']) ? (string)$_GET['return_to'] : '';

// Capture et mémorisation de la page d'origine pour redirection post-callback
$returnTo = $returnToGet;
if ($returnTo === '') {
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref !== '') {
        $path = parse_url($ref, PHP_URL_PATH);
        if (is_string($path) && $path !== '') { $returnTo = $path; }
    }
}
if ($returnTo === '') { $returnTo = '/html/add_assets.php'; }

// Mettre à jour le mapping state -> user avec return_to
$rawStates = @file_get_contents($stateFile);
$statesUpd = json_decode((string)$rawStates, true);
if (!is_array($statesUpd)) { $statesUpd = []; }
$statesUpd[$state] = [
    'user_id'    => $userId,
    'created_at' => date('c'),
    'return_to'  => $returnTo,
];
@file_put_contents($stateFile, json_encode($statesUpd, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

// Base et constantes (respecter strictement redirect_uri en local)
$baseUrl      = 'https://webview.powens.com/fr/connect';
$domainParam  = 'nova-sandbox.biapi.pro';
$clientId     = '9941084';
$redirectUri  = 'http://localhost:8000/html/powens_callback.php';

// Priorité d’assemblage: 1) static_link explicite, 2) params GET, 3) fallback env, 4) fallback par défaut (banque)
$staticLink = '';
if ($staticLinkGet !== '') {
    $staticLink = $staticLinkGet;
} elseif ($capabilities !== '' && ($accountTypes !== '' || $accountType !== '')) {
    $params = [
        'domain'                 => $domainParam,
        'client_id'              => $clientId,
        'redirect_uri'           => $redirectUri,
        'connector_capabilities' => $capabilities,
    ];
    // Pour certains environnements, indiquer explicitement le usecase wealth
    if (stripos($capabilities, 'bankwealth') !== false) {
        $params['usecase'] = 'wealth';
    }
    if ($accountTypes !== '') {
        $params['account_types'] = $accountTypes;
    } else {
        $params['account_type'] = $accountType;
    }
    $qs = http_build_query($params);
    $staticLink = $baseUrl . '?' . $qs;
} else {
    $envLink = getenv('POWENS_STATIC_LINK');
    if ($envLink && trim($envLink) !== '') {
        $staticLink = $envLink;
    } else {
        // Défault: flux banque avec carte
        $staticLink = $baseUrl . '?domain=' . rawurlencode($domainParam)
            . '&client_id=' . rawurlencode($clientId)
            . '&redirect_uri=' . rawurlencode($redirectUri)
            . '&connector_capabilities=bank&account_types=card';
    }
}

// Ajouter le state à l’URL (sans toucher redirect_uri)
$separator = (strpos($staticLink, '?') !== false) ? '&' : '?';
$target = $staticLink . $separator . 'state=' . urlencode($state);

// Mode debug: afficher l'URL construite au lieu de rediriger
if ($debugRequested === '1') {
    header('Content-Type: text/plain; charset=utf-8');
    echo "staticLink=\n" . $staticLink . "\n\n";
    echo "target=\n" . $target . "\n";
    exit;
}

header('Location: ' . $target, true, 302);
exit;