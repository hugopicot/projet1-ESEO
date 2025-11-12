<?php
global $pdo;
session_start();
require_once '../config/config.php';
require_once __DIR__ . '/../api/market_data.php';


header('Content-Type: text/html; charset=UTF-8');

if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth.php");
    exit;
}

$stripeSessionId = isset($_GET['session_id']) ? trim((string)$_GET['session_id']) : '';
$planParam = isset($_GET['plan']) ? trim((string)$_GET['plan']) : '';
if ($stripeSessionId !== '') {
    $plan = in_array($planParam, ['starter', 'pro'], true) ? $planParam : 'starter';
    $now = date('Y-m-d H:i:s');
    $end = date('Y-m-d H:i:s', strtotime('+30 days'));


    try { $pdo->exec("ALTER TABLE users ADD COLUMN subscription_plan VARCHAR(50) NOT NULL DEFAULT 'none'"); } catch (Throwable $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN subscription_start DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00'"); } catch (Throwable $e) {}
    try { $pdo->exec("ALTER TABLE users ADD COLUMN subscription_end DATETIME NOT NULL DEFAULT '1970-01-01 00:00:00'"); } catch (Throwable $e) {}

    try {
        $stmt = $pdo->prepare("UPDATE users SET subscription_plan = ?, subscription_start = ?, subscription_end = ? WHERE id = ?");
        $stmt->execute([$plan, $now, $end, $_SESSION['user_id']]);
        $_SESSION['subscription_activated'] = true;
    } catch (Throwable $e) {

    }
}


$user = null;
try {
    $stmt = $pdo->prepare("SELECT subscription_plan, subscription_end FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch (PDOException $e) {

    $user = ['subscription_plan' => 'none', 'subscription_end' => '1970-01-01'];
}

if (!$user || ($user['subscription_plan'] ?? 'none') === 'none' || strtotime($user['subscription_end'] ?? '1970-01-01') < time()) {
 
    header("Location: subscription.php");
    exit;
}

$plan = isset($user['subscription_plan']) && $user['subscription_plan'] !== '' ? $user['subscription_plan'] : 'starter';

$username = trim((string)($_SESSION['username'] ?? ''));
if ($username === '') {
    $username = 'Compte Nova';
}

$userEmail = trim((string)($_SESSION['user_email'] ?? ''));

try { refresh_manual_assets($pdo, (int)$_SESSION['user_id'], 300); } catch (Throwable $e) { /* ignore */ }


$portfolioInvestedEur = 0.0; $portfolioCurrentEur = 0.0; $portfolioGainEur = 0.0; $portfolioGainPct = 0.0;
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
    $cs = $pdo->prepare("SELECT COALESCE(SUM(balance_eur),0) FROM compte WHERE user_id = ? AND source='manual' AND status='active'");
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
    <link rel="stylesheet" href="../css/patrimoine.css">

</head>

<body>

<div class="app-shell">

    <header class="app-navbar" role="banner">

        <div class="navbar-brand">

            <a href="app.php" title="logo">
                <img class="navbar-logo" src="../asset/img/logo.png" alt="Nova">
            </a>
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

            <div class="dropdown" data-dropdown>

                <button class="icon-button avatar-button" type="button" aria-haspopup="menu" aria-expanded="false" data-dropdown-toggle="navbar-user-menu">

                    <span class="avatar avatar-square" aria-hidden="true">ER</span>

                    <span class="sr-only">Ouvrir le menu utilisateur</span>

                </button>

                <div class="dropdown-menu bottom-end" id="navbar-user-menu" role="menu">
                    <a class="dropdown-item" href="./profil.php" role="menuitem">

                    <a class="dropdown-item" href="update_profile.php" role="menuitem">

                        <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/user.svg" alt="" loading="lazy">

                        <span class="dropdown-label">Mon profil</span>

                    </a>

                    <a class="dropdown-item" href="#parametres" role="menuitem">

                        <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/cog-8-tooth.svg" alt="" loading="lazy">

                        <span class="dropdown-label">Paramètres</span>

                    </a>

                    <div class="dropdown-divider" role="none"></div>

                    <a class="dropdown-item" href="PDC.php" role="menuitem">

                        <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/shield-check.svg" alt="" loading="lazy">

                        <span class="dropdown-label">Politique de confidentialité</span>

                    </a>

                    <a class="dropdown-item" href="#feedback" role="menuitem">

                        <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/light-bulb.svg" alt="" loading="lazy">

                        <span class="dropdown-label">Partager un feedback</span>

                    </a>

                    <div class="dropdown-divider" role="none"></div>

                    <a class="dropdown-item" href="../index.php" role="menuitem">

                        <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/arrow-right-start-on-rectangle.svg" alt="" loading="lazy">

                        <span class="dropdown-label">Se déconnecter</span>

                    </a>

                </div>

            </div>

        </nav>

    </header>



    <div class="app-layout">

        <aside class="app-sidebar" aria-label="Navigation secondaire">

            <div class="sidebar-body">

                <div class="sidebar-section">

                    <a class="sidebar-item is-active" href="app.php" aria-current="page">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/home.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Synthèse</span>

                    </a>

                    <a class="sidebar-item" href="patrimoine.php">

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
                            <?php if ($userEmail !== ''): ?>
                                <span class="sidebar-user-email"><?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
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

                        <a class="dropdown-item" href="./settings.php" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/cog-8-tooth.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Paramètres</span>

                        </a>

                        <div class="dropdown-divider" role="none"></div>

                        <a class="dropdown-item" href="PDC.php" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/shield-check.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Politique de confidentialité</span>

                        </a>

                        <a class="dropdown-item" href="#feedback" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/light-bulb.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Partager un feedback</span>

                        </a>

                        <div class="dropdown-divider" role="none"></div>
                        <a class="dropdown-item" href="../index.php" role="menuitem">
                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/arrow-right-start-on-rectangle.svg" alt="" loading="lazy">
                            <span class="dropdown-label">Se déconnecter</span>
                        </a>

                    </div>

                </div>

            </div>

        </aside>



        <main class="app-content" id="main-content" tabindex="-1">

            <section class="content-section">

                <div class="content-grid dashboard-grid">

                    <article class="content-card content-card--chart">

                        <h2>Evolution des indicateurs</h2>

                        <div id="chart-range-container" class="chart-container"></div>

                    </article>

                    <article class="content-card content-card--performance">

                        <header class="performance-header">

                            <div class="performance-title">

                                <h2>Performance</h2>
                            </div>

                            <button type="button" class="performance-toggle" aria-label="Afficher plus d'options">

                                <span class="sr-only">Afficher plus d'options</span>

                                <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">

                                    <path d="M7 10l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>

                                </svg>

                            </button>

                        </header>

                        <div class="performance-metric">
                            <?php
                                $gainSign = $portfolioGainEur >= 0 ? '+' : '−';
                                $gainAbs = abs($portfolioGainEur);
                                $formattedGain = number_format($gainAbs, 0, ',', ' ');
                                $formattedPct = number_format(abs($portfolioGainPct), 2, ',', ' ');
                            ?>
                            <span class="metric-label">Plus-value &middot; Tout</span>
                            <span class="metric-value"><?= $gainSign ?><?= htmlspecialchars($formattedGain, ENT_QUOTES, 'UTF-8') ?>&nbsp;EUR</span>
                            <span class="metric-variation"><?= $gainSign ?><?= htmlspecialchars($formattedPct, ENT_QUOTES, 'UTF-8') ?>&nbsp;%</span>
                        </div>

                        

                    </article>

                </div>


            </section>

        </main>

</div>

</div>

<script src="https://unpkg.com/lightweight-charts@4.1.1/dist/lightweight-charts.standalone.production.js"></script>
<script src="../js/pages/app.js"></script>
</body>

</html>

