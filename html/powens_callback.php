<?php
// Callback simplifié: lit code/erreur, récupère return_to depuis la session et redirige.
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Domaine de base
$scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
$APP_DOMAIN = $scheme . '://' . $host;

// Paramètres du provider
$code          = isset($_GET['code']) ? $_GET['code'] : null;
$connection_id = isset($_GET['connection_id']) ? $_GET['connection_id'] : null;
$state         = isset($_GET['state']) ? $_GET['state'] : null;
$error         = isset($_GET['error']) ? $_GET['error'] : null;

// Page de retour depuis la session (fallback défaut)
$returnToPath = isset($_SESSION['powens_return_to']) ? (string)$_SESSION['powens_return_to'] : '/html/add_assets.php';

// Stockage minimal (debug/local)
$_SESSION['powens_callback'] = [
    'received_at'   => date('c'),
    'code'          => $code,
    'connection_id' => $connection_id,
    'state'         => $state,
    'error'         => $error,
];

// Redirection vers la page d'origine
if ($code && !$error) {
    $target = (strpos($returnToPath, 'http') === 0) ? $returnToPath : ($APP_DOMAIN . $returnToPath);
    $sep = (strpos($target, '?') !== false) ? '&' : '?';
    $target .= $sep . 'powens_status=success';
    if ($connection_id) { $target .= '&connection_id=' . urlencode((string)$connection_id); }
    if ($state) { $target .= '&state=' . urlencode((string)$state); }
    header('Location: ' . $target, true, 302);
    exit;
}

if ($error) {
    $target = (strpos($returnToPath, 'http') === 0) ? $returnToPath : ($APP_DOMAIN . $returnToPath);
    $sep = (strpos($target, '?') !== false) ? '&' : '?';
    $target .= $sep . 'powens_status=error&error=' . urlencode((string)$error);
    if ($state) { $target .= '&state=' . urlencode((string)$state); }
    header('Location: ' . $target, true, 302);
    exit;
}

// Page simple si aucun paramètre utile reçu
http_response_code(200);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Callback Powens</title>
    <link rel="stylesheet" href="../css/app.css" />
    <link rel="stylesheet" href="../css/styles.css" />
    <style>
        .callback-wrap { max-width: 760px; margin: 48px auto; padding: 24px; border-radius: 12px; background: var(--surface-2, #111); color: var(--text-primary, #fff); }
        .callback-wrap h1 { font-size: 1.5rem; margin: 0 0 12px; }
        .callback-wrap p { margin: 6px 0; color: var(--text-secondary, #bbb); }
        .callback-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }
        .callback-item { padding: 12px; border-radius: 8px; background: var(--surface-1, #0b0b0b); border: 1px solid var(--border-color, #222); }
        .label { font-weight: 600; color: var(--text-primary, #fff); }
        .value { color: var(--text-secondary, #bbb); word-break: break-all; }
        .hint { margin-top: 16px; font-size: 0.95rem; color: var(--text-secondary, #bbb); }
    </style>
    <link rel="icon" type="image/png" href="../asset/img/logo.png" />
    <link rel="apple-touch-icon" href="../asset/img/logo.png" />
    <meta name="color-scheme" content="dark light" />
    <meta name="robots" content="noindex" />
    <meta name="referrer" content="no-referrer" />
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />
    <meta name="description" content="Callback Powens pour environnement local de développement." />
    <meta name="author" content="Projet ESEO" />
    <meta name="theme-color" content="#0b0b0b" />
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
    <meta name="format-detection" content="telephone=no" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline';" />
    <meta http-equiv="Referrer-Policy" content="no-referrer" />
</head>
<body>
    <main class="callback-wrap">
        <h1>Callback Powens – Environnement local</h1>
        <?php if ($error): ?>
            <p>Erreur: <strong><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></strong></p>
        <?php else: ?>
            <p>Aucun code reçu. Cet endpoint est prêt et accessible en local.</p>
            <p class="hint">Lorsque le provider redirige avec un <code>code</code>, vous serez renvoyé vers la page où vous étiez.</p>
        <?php endif; ?>

        <div class="callback-grid">
            <div class="callback-item"><div class="label">Code</div><div class="value"><?php echo htmlspecialchars((string)$code, ENT_QUOTES, 'UTF-8'); ?></div></div>
            <div class="callback-item"><div class="label">Connection ID</div><div class="value"><?php echo htmlspecialchars((string)$connection_id, ENT_QUOTES, 'UTF-8'); ?></div></div>
            <div class="callback-item"><div class="label">State</div><div class="value"><?php echo htmlspecialchars((string)$state, ENT_QUOTES, 'UTF-8'); ?></div></div>
            <div class="callback-item"><div class="label">Hôte</div><div class="value"><?php echo htmlspecialchars($host, ENT_QUOTES, 'UTF-8'); ?></div></div>
        </div>

        <p class="hint">Pour un comportement universel en local, utilisez <code>http://localhost:8000/html/powens_callback.php</code> comme URL de redirection.</p>
    </main>
</body>
</html>