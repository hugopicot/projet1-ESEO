<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth.php");
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/market_data.php';
try { refresh_manual_assets($pdo, (int)$_SESSION['user_id'], 300); } catch (Throwable $e) {  }

header('Content-Type: text/html; charset=UTF-8');

$username = trim((string)($_SESSION['username'] ?? ''));
if ($username === '') {
    $username = 'Compte Nova';
}

$userEmail = trim((string)($_SESSION['user_email'] ?? ''));

$plan = 'starter';
try {
    $st = $pdo->prepare("SELECT subscription_plan FROM users WHERE id = ?");
    $st->execute([$_SESSION['user_id']]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['subscription_plan']) && $row['subscription_plan'] !== '') {
        $plan = $row['subscription_plan'];
    }
} catch (Throwable $e) { /* plan par défaut */ }

$assetsData = [];
try {
    $sql = "SELECT pm.external_id,
                   COALESCE(pm.name, pm.external_id) AS name,
                   pm.quantity,
                   pm.purchase_price,
                   SUM(COALESCE(c.balance_eur, 0)) AS total_eur,
                   pm.currency,
                   c.categorie
            FROM compte c
            LEFT JOIN positions_manual pm
              ON pm.user_id = c.user_id
             AND pm.categorie = c.categorie
             AND pm.external_id = c.external_id
            WHERE c.user_id = ?
              AND c.status = 'active'
              AND c.source = 'manual'
              AND c.categorie IN ('crypto','actions_fonds')
            GROUP BY pm.external_id, pm.name, pm.quantity, pm.purchase_price
            ORDER BY total_eur DESC";
    $q = $pdo->prepare($sql);
    $q->execute([$_SESSION['user_id']]);
    $rows = $q->fetchAll(PDO::FETCH_ASSOC) ?: [];
    $grandTotal = 0.0;
    foreach ($rows as $r) { $grandTotal += (float)$r['total_eur']; }
    $palette = ['#8b5cf6','#3b82f6','#ef4444','#10b981','#f59e0b','#06b6d4','#64748b','#a78bfa','#fb7185','#22c55e','#f97316','#84cc16'];
    $i = 0;
    foreach ($rows as $r) {
        $name = (string)($r['name'] ?? '');
        if ($name === '') { $name = (string)$r['external_id']; }
        $ext = (string)$r['external_id'];
        $qty = isset($r['quantity']) ? (float)$r['quantity'] : 0.0;
        $buyUnit = isset($r['purchase_price']) ? (float)$r['purchase_price'] : null;
        $cur = strtoupper((string)($r['currency'] ?? 'EUR'));
        $buyTotalNative = ($buyUnit !== null && $qty > 0) ? ($buyUnit * $qty) : null;
        $buyTotal = ($buyTotalNative !== null) ? ($cur === 'EUR' ? $buyTotalNative : fx_convert_amount($buyTotalNative, $cur, 'EUR')) : null;
        $val = isset($r['total_eur']) ? (float)$r['total_eur'] : 0.0;
        $percent = $grandTotal > 0 ? round($val / $grandTotal * 100, 2) : 0.0;
        $deltaValue = ($buyTotal !== null) ? ($val - $buyTotal) : null;
        $deltaPercent = ($buyTotal !== null && $buyTotal > 0) ? round(($val - $buyTotal) / $buyTotal * 100, 2) : null;
        $assetsData[] = [
            'external_id' => $ext,
            'name' => $name,
            'share' => $percent,
            'value' => $val,
            'deltaValue' => $deltaValue,
            'deltaPercent' => $deltaPercent,
            'color' => $palette[$i % count($palette)],
        ];
        $i++;
    }
} catch (Throwable $e) {
}

$portfolioInvestedEur = 0.0;
$portfolioCurrentEur = 0.0;
try {
    $ps = $pdo->prepare("SELECT quantity, purchase_price, currency FROM positions_manual WHERE user_id = ?");
    $ps->execute([$_SESSION['user_id']]);
    $posRows = $ps->fetchAll(PDO::FETCH_ASSOC) ?: [];
    foreach ($posRows as $p) {
        $qty = isset($p['quantity']) ? (float)$p['quantity'] : 0.0;
        $buyUnit = isset($p['purchase_price']) ? (float)$p['purchase_price'] : 0.0;
        $cur = strtoupper((string)($p['currency'] ?? 'EUR'));
        if ($qty > 0 && $buyUnit > 0) {
            $native = $qty * $buyUnit;
            $portfolioInvestedEur += ($cur === 'EUR') ? $native : fx_convert_amount($native, $cur, 'EUR');
        }
    }
    $cs = $pdo->prepare("SELECT COALESCE(SUM(balance_eur),0) AS total FROM compte WHERE user_id = ? AND source='manual' AND status='active'");
    $cs->execute([$_SESSION['user_id']]);
    $portfolioCurrentEur = (float)($cs->fetchColumn() ?: 0.0);
} catch (Throwable $e) { /* ignore */ }
$portfolioGainEur = $portfolioCurrentEur - $portfolioInvestedEur;
$portfolioGainPct = ($portfolioInvestedEur > 0) ? round(($portfolioGainEur / $portfolioInvestedEur) * 100, 2) : 0.0;
?>
<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova · Console</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/app.css">
    <?php $patCssVer = @filemtime(__DIR__ . '/../css/patrimoine.css'); ?>
    <link rel="stylesheet" href="../css/patrimoine.css?v=<?= (int)($patCssVer ?: time()) ?>">

</head>

<body>

<div class="app-shell">

    <header class="app-navbar" role="banner">

        <div class="navbar-brand">

            <img src="../asset/img/logo.png" alt="Nova" class="navbar-logo">

            <span class="navbar-title">Nova Console</span>

        </div>

        <div class="navbar-spacer" aria-hidden="true"></div>

        <nav class="navbar-actions" aria-label="Actions principales">

            <a class="icon-button" href="#search" aria-label="Ouvrir la recherche">

                <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/magnifying-glass.svg" alt="" loading="lazy">

            </a>

            <a class="icon-button" href="#inbox" aria-label="Consulter la messagerie">

                <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/inbox.svg" alt="" loading="lazy">

            </a>

  <a class="icon-button" href="manual_crypto.php" aria-label="Ajouter des actifs" title="Ajouter des actifs">
                <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 5a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h5V6a1 1 0 011-1z" />
                </svg>
                <span class="sr-only">Ajouter des actifs</span>
            </a>

        </nav>

    </header>

    <div class="app-layout">

        <aside class="app-sidebar" aria-label="Navigation secondaire">

            <div class="sidebar-body">

                <div class="sidebar-section">

                    <a class="sidebar-item" href="app.php">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/home.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Synthèse</span>

                    </a>

                    <a class="sidebar-item is-active" href="patrimoine.php" aria-current="page">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/square-2-stack.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Patrimoine</span>

                    </a>

                    <a class="sidebar-item" href="analyse.php">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/ticket.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Analyse</span>

                    </a>

                </div>

                <div class="sidebar-spacer" aria-hidden="true"></div>

                <div class="sidebar-section">

                </div>

            </div>

            <div class="sidebar-footer only-desktop">

                <div class="dropdown full-width" data-dropdown>

                    <button class="sidebar-item sidebar-button" type="button" aria-haspopup="menu" aria-expanded="false" data-dropdown-toggle="profile-menu">

                        <span class="avatar avatar-square avatar-large" aria-hidden="true">ER</span>

                        <span class="sidebar-label sidebar-user">

                            <span class="sidebar-user-name"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></span>

                            <span class="plan-badge" title="Votre plan"><?= ($plan === 'pro' ? 'Pro' : 'Starter') ?></span>

                            <span class="sidebar-user-email"><?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></span>

                        </span>

                        <img class="icon icon-chevron" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/chevron-up.svg" alt="" loading="lazy">

                        <img class="icon icon-chevron icon-chevron-alt" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/chevron-down.svg" alt="" loading="lazy">

                        <span class="sr-only">Ouvrir le menu profil</span>

                    </button>

                    <div class="dropdown-menu top-start" id="profile-menu" role="menu">

                        <a class="dropdown-item" href="update_profile.php" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/user.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Mon profil</span>

                        </a>

                        <a class="dropdown-item" href="settings.php" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/cog-8-tooth.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Paramètres</span>

                        </a>

                        <div class="dropdown-divider" role="none"></div>

                        <a class="dropdown-item" href="PDC.php" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/shield-check.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Politique de confidentialité</span>

                        </a>

                        <div class="dropdown-divider" role="none"></div>

                        <a class="dropdown-item" href="logout.php" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/arrow-right-start-on-rectangle.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Se déconnecter</span>

                        </a>

                    </div>

                </div>

            </div>

        </aside>

        <main class="app-content" id="main-content" tabindex="-1">

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success" role="status" aria-live="polite">
                    <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php elseif (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger" role="status" aria-live="polite">
                    <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <section class="content-section">

                <div class="content-grid dashboard-grid">

                    <article class="content-card content-card--chart">

                        <h2>Evolution des indicateurs</h2>
                        <div id="portfolio-chart" class="chart-container"></div>

                    </article>

                    <article class="content-card content-card--performance">

                        <header class="performance-header">

                            <div class="performance-title">
                                <h2>Performance</h2>
                            </div>

                        </header>

                        <div class="performance-metric">
                            <span class="metric-label">Plus-value</span>
                            <?php
                                $gainSign = $portfolioGainEur >= 0 ? '+' : '-';
                                $gainAbs = abs($portfolioGainEur);
                                $formattedGain = number_format($gainAbs, 0, ',', ' ');
                                $formattedPct = number_format(abs($portfolioGainPct), 2, ',', ' ');
                            ?>
                            <span class="metric-value"><?= $gainSign ?><?= htmlspecialchars($formattedGain, ENT_QUOTES, 'UTF-8') ?>&nbsp;EUR</span>
                            <span class="metric-variation"><?= $gainSign ?><?= htmlspecialchars($formattedPct, ENT_QUOTES, 'UTF-8') ?>&nbsp;%</span>
                        </div>

                        <div class="performance-note" style="margin-top:10px;color:#6b7280;font-size:14px;">
                            <span>La plus-value affichée correspond à la différence entre la valeur actuelle et le prix d’achat cumulé. Elle exclut frais et impôts.</span>
                        </div>

                    </article>

                </div>

            </section>

            <section class="content-section">
                <div class="assets-section">
                    <div class="assets-header">
                        <div class="assets-header-text">
                            <h2>Actifs</h2>
                            <p>Suivez la répartition de votre patrimoine.</p>
                        </div>
                    </div>
                    <?php
                        $toastSuccess = $_SESSION['success'] ?? null;
                        $toastError = $_SESSION['error'] ?? null;
                        unset($_SESSION['success'], $_SESSION['error']);
                    ?>
                    <script type="application/json" id="initial-toasts">
                    <?= json_encode(['success' => $toastSuccess, 'error' => $toastError], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
                    </script>
                    <script type="application/json" id="assets-data">
                    <?= json_encode($assetsData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
                    </script>
                    <div id="toast-container" class="toast-container" aria-live="polite" aria-atomic="true"></div>
                    <div class="assets-layout">
                        <div class="assets-table">
                            <div class="assets-table-header">
                                <span>Nom</span>
                                <span>Repartition</span>
                                <span>Valeur</span>
                                <span>+/- value Tout</span>
                            </div>
                            <ul class="assets-rows" id="assets-list" aria-label="Repartition des actifs"></ul>
                        </div>
                        <div class="assets-chart-card">
                            <div class="assets-donut-wrapper">
                                <div class="assets-donut-ring" id="assets-donut-ring" role="img" aria-label="Diagramme des actifs"></div>
                                <div class="assets-donut-center">
                                    <span class="assets-donut-value" id="assets-total-value">--</span>
                                    <span class="assets-donut-label">Total</span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </section>

        </main>

</div>

</div>

<script src="https://unpkg.com/lightweight-charts@4.1.1/dist/lightweight-charts.standalone.production.js"></script>
<script src="../js/pages/patrimoine.js"></script>
</body>

</html>

