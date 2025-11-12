<?php
session_start();
require_once '../config/config.php';
require_once '../api/market_data.php';
global $pdo;

if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth.php");
    exit;
}

$username = trim((string)($_SESSION['username'] ?? ''));
if ($username === '') {
    $username = 'Compte Nova';
}

$userEmail = trim((string)($_SESSION['user_email'] ?? ''));
header('Content-Type: text/html; charset=UTF-8');

$plan = 'starter';
try {
    $st = $pdo->prepare("SELECT subscription_plan FROM users WHERE id = ?");
    $st->execute([$_SESSION['user_id']]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['subscription_plan']) && $row['subscription_plan'] !== '') {
        $plan = $row['subscription_plan'];
    }
} catch (Throwable $e) { /* plan par défaut */ }

function portfolioDailyTotals(PDO $pdo, int $userId, int $limit = 10): array {
    try {
        $q = "SELECT DATE(created_at) AS d, SUM(value_eur) AS t FROM valuations_history WHERE user_id = ? GROUP BY DATE(created_at) ORDER BY d DESC LIMIT " . intval($limit);
        $st = $pdo->prepare($q);
        $st->execute([$userId]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        return $rows;
    } catch (Throwable $e) { return []; }
}

function hhiScore(PDO $pdo, int $userId): array {
    try {
        $st = $pdo->prepare("SELECT DATE(created_at) AS d FROM valuations_history WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
        $st->execute([$userId]);
        $last = $st->fetch(PDO::FETCH_ASSOC);
        if (!$last || !isset($last['d'])) return ['score' => null, 'label' => 'N/A'];
        $day = $last['d'];
        $st2 = $pdo->prepare("SELECT external_id, SUM(value_eur) AS v FROM valuations_history WHERE user_id = ? AND DATE(created_at) = ? GROUP BY external_id");
        $st2->execute([$userId, $day]);
        $rows = $st2->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $total = 0; foreach ($rows as $r) { $total += floatval($r['v']); }
        if ($total <= 0) return ['score' => null, 'label' => 'N/A'];
        $hhi = 0; foreach ($rows as $r) { $w = floatval($r['v']) / $total; $hhi += $w * $w; }
        $label = ($hhi < 0.2) ? 'Faible' : (($hhi < 0.4) ? 'Moyenne' : 'Élevée');
        return ['score' => $hhi, 'label' => $label];
    } catch (Throwable $e) { return ['score' => null, 'label' => 'N/A']; }
}

function topMovers24h(PDO $pdo, int $userId, int $count = 3): array {
    try {
        $q = "SELECT external_id, DATE(created_at) AS d, SUM(value_eur) AS v FROM valuations_history WHERE user_id = ? GROUP BY external_id, DATE(created_at) ORDER BY d DESC";
        $st = $pdo->prepare($q);
        $st->execute([$userId]);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        $byAsset = [];
        foreach ($rows as $r) {
            $eid = (string)$r['external_id']; $d = (string)$r['d']; $v = floatval($r['v']);
            if (!isset($byAsset[$eid])) $byAsset[$eid] = [];
            $byAsset[$eid][$d] = $v;
        }
        $changes = [];
        foreach ($byAsset as $eid => $dmap) {
            $dates = array_keys($dmap); sort($dates);
            $n = count($dates);
            if ($n < 2) continue;
            $vPrev = $dmap[$dates[$n-2]]; $vLast = $dmap[$dates[$n-1]];
            if ($vPrev <= 0) continue;
            $pct = ($vLast / $vPrev) - 1.0;
            $changes[] = ['asset' => $eid, 'pct' => $pct];
        }
        usort($changes, function($a,$b){ return ($b['pct'] <=> $a['pct']); });
        $gagnants = array_slice($changes, 0, $count);
        usort($changes, function($a,$b){ return ($a['pct'] <=> $b['pct']); });
        $perdants = array_slice($changes, 0, $count);
        return ['up' => $gagnants, 'down' => $perdants];
    } catch (Throwable $e) { return ['up' => [], 'down' => []]; }
}

$daily = portfolioDailyTotals($pdo, (int)$_SESSION['user_id'], 10);
$var24Pct = null; $var24Abs = null; $perf7j = null;
if (count($daily) >= 2) {
    $t0 = floatval($daily[0]['t']); $t1 = floatval($daily[1]['t']);
    if ($t1 > 0) { $var24Pct = ($t0 / $t1 - 1.0) * 100.0; $var24Abs = $t0 - $t1; }
}
if (count($daily) >= 8) {
    $t0 = floatval($daily[0]['t']); $t7 = floatval($daily[7]['t']);
    if ($t7 > 0) { $perf7j = ($t0 / $t7 - 1.0) * 100.0; }
}
$hhi = hhiScore($pdo, (int)$_SESSION['user_id']);
$movers = topMovers24h($pdo, (int)$_SESSION['user_id']);

function simple_json(string $url): ?array {
    if (function_exists('http_json_retry')) {
        $res = http_json_retry($url, 10, 3, 200);
        if ($res['status'] >= 200 && $res['status'] < 300) {
            if (isset($res['data']) && is_array($res['data'])) { return $res['data']; }
            $raw = $res['raw'] ?? '';
            $data = @json_decode((string)$raw, true);
            return is_array($data) ? $data : null;
        }
        return null;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'NovaMarketData/1.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    if (defined('CURL_SSLVERSION_TLSv1_2')) {
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    }
    $caBundle = realpath(__DIR__ . '/../config/cacert.pem');
    if ($caBundle && is_file($caBundle)) { curl_setopt($ch, CURLOPT_CAINFO, $caBundle); }
    $body = curl_exec($ch);
    curl_close($ch);
    $data = @json_decode((string)$body, true);
    return is_array($data) ? $data : null;
}

function market_today(int $perPage = 50): array {
    $perPage = max(10, $perPage);
    $url = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=eur&order=market_cap_desc&per_page=' . $perPage . '&page=1&sparkline=false&price_change_percentage=24h,7d';
    $data = simple_json($url) ?: [];
    $wins = []; $losses = []; $heat = [];
    $stableSyms = ['USDT','USDC','DAI','BUSD','FRAX','TUSD','EURS'];
    foreach ($data as $c) {
        $sym = strtoupper((string)($c['symbol'] ?? ''));
        $name = (string)($c['name'] ?? '');
        $cap = isset($c['market_cap']) ? (float)$c['market_cap'] : 0.0;
        $vol = isset($c['total_volume']) ? (float)$c['total_volume'] : 0.0;
        if (in_array($sym, $stableSyms, true)) { continue; }
        if ($cap < 200_000_000 || $vol < 20_000_000) { continue; }

        $chg24 = null;
        if (isset($c['price_change_percentage_24h'])) { $chg24 = (float)$c['price_change_percentage_24h']; }
        if ($chg24 === null && isset($c['price_change_percentage_24h_in_currency'])) { $chg24 = (float)$c['price_change_percentage_24h_in_currency']; }
        if ($chg24 === null) { $chg24 = 0.0; }
        $chg7 = null;
        if (isset($c['price_change_percentage_7d'])) { $chg7 = (float)$c['price_change_percentage_7d']; }
        if ($chg7 === null && isset($c['price_change_percentage_7d_in_currency'])) { $chg7 = (float)$c['price_change_percentage_7d_in_currency']; }
        if ($chg7 === null) { $chg7 = 0.0; }

        $score = 0.7 * $chg24 + 0.3 * $chg7;
        $item = [
            'id' => (string)($c['id'] ?? ''),
            'name' => $name,
            'symbol' => $sym,
            'image' => (string)($c['image'] ?? ''),
            'price' => isset($c['current_price']) ? (float)$c['current_price'] : null,
            'chg' => (float)$chg24,
            'score' => (float)$score,
        ];
        if ($score >= 0) { $wins[] = $item; } else { $losses[] = $item; }
    }
    usort($wins, function($a,$b){ return ($b['score'] <=> $a['score']); });
    usort($losses, function($a,$b){ return ($a['score'] <=> $b['score']); });
    $heat = array_slice($data, 0, 12);
    return [
        'wins' => array_slice($wins, 0, 3),
        'losses' => array_slice($losses, 0, 3),
        'heat' => array_map(function($c){
            $chg24 = 0.0;
            if (isset($c['price_change_percentage_24h'])) { $chg24 = (float)$c['price_change_percentage_24h']; }
            elseif (isset($c['price_change_percentage_24h_in_currency'])) { $chg24 = (float)$c['price_change_percentage_24h_in_currency']; }
            return [
                'symbol' => strtoupper((string)($c['symbol'] ?? '')),
                'image' => (string)($c['image'] ?? ''),
                'chg' => $chg24,
            ];
        }, $heat)
    ];
}

$mt = market_today(50);
$topWins = $mt['wins'];
$topLosses = $mt['losses'];
$heatItems = $mt['heat'];
?>
<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Nova · Analyse</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="../css/patrimoine.css">
    <link rel="stylesheet" href="../css/analyse.css">

    <style>
         /* Animation pour le dessin de la courbe */
         @keyframes drawCurve {
             from {
                 stroke-dashoffset: 100%;
             }
             to {
                 stroke-dashoffset: 0;
             }
         }

         /* Style pour la courbe principale - Premium look */
         #projection-main {
             filter: drop-shadow(0 0 6px rgba(255, 215, 0, 0.25));
             transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
         }

         #projection-main:hover {
             filter: drop-shadow(0 0 10px rgba(255, 215, 0, 0.4));
             stroke-width: 3.2;
         }

         /* Animation pour l'aire sous la courbe */
         #projection-area {
             transition: opacity 0.6s ease-in-out;
         }

         /* Style pour les points animés */
         .animated-point {
             transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
         }

         .animated-point:hover {
             r: 6;
             filter: drop-shadow(0 0 8px rgba(255, 215, 0, 0.6));
         }

         /* Style pour les labels d'axes - Meilleure lisibilité */
         .y-axis text, .time-axis text {
             transition: all 0.3s ease;
             font-weight: 400;
             letter-spacing: 0.3px;
         }

         .y-axis text:hover, .time-axis text:hover {
             fill: rgba(255,255,255,0.8);
             font-weight: 500;
         }

         /* Effet de glow subtil pour le graphique */
         .chart-svg {
             background: radial-gradient(ellipse at center, rgba(255, 215, 0, 0.015) 0%, transparent 70%);
             border-radius: 8px;
         }

         /* Amélioration du conteneur du graphique */
         .chart-container {
             background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(255,215,0,0.01) 100%);
             border-radius: 12px;
             padding: 8px;
             box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
         }

         /* Animation d'entrée pour les éléments du graphique */
         @keyframes fadeInUp {
             from {
                 opacity: 0;
                 transform: translateY(10px);
             }
             to {
                 opacity: 1;
                 transform: translateY(0);
             }
         }

         .y-axis text, .time-axis text {
             animation: fadeInUp 0.6s ease-out forwards;
         }

         /* Responsive design pour les petits écrans */
         @media (max-width: 768px) {
             .chart-svg {
                 font-size: 9px;
             }

             #projection-main {
                 stroke-width: 2.2;
             }
         }
     </style>

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

    <a class="icon-button" href="http://hugo:8090/html/manual_crypto.php" aria-label="Ajouter des actifs" title="Ajouter des actifs">
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

                    <a class="sidebar-item" href="patrimoine.php">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/square-2-stack.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Patrimoine</span>

                    </a>

                    <a class="sidebar-item is-active" href="analyse.php" aria-current="page">

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

            <section class="analysis-section">
                <div class="section-header">
                    <h1 class="section-title">Analyse de votre portefeuille</h1>
                    <p class="section-subtitle">Découvrez les performances et la répartition de vos investissements</p>
                </div>

                <div class="analysis-cards-grid">

                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Variation 24h</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div class="analysis-value-main"><?= ($var24Pct !== null ? sprintf('%+.2f%%', $var24Pct) : '—') ?></div>
                                <div class="analysis-value-label">Évolution totale du portefeuille sur 24h</div>
                            </div>
                        </div>
                    </div>

                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Performance 7 jours</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div class="analysis-value-main"><?= ($perf7j !== null ? sprintf('%+.2f%%', $perf7j) : '—') ?></div>
                                <div class="analysis-value-label">Variation cumulée sur 7 jours</div>
                            </div>
                        </div>
                    </div>

                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Diversification (HHI)</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div class="analysis-value-main"><?= ($hhi['score'] !== null ? number_format($hhi['score'], 3) : '—') ?></div>
                                <div class="analysis-value-label">Concentration: <?= htmlspecialchars($hhi['label']) ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Top Movers 24h</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <?php if (!empty($movers['up']) || !empty($movers['down'])): ?>
                                    <div class="analysis-value-label">
                                        <?php foreach ($movers['up'] as $mv): ?>
                                            <div>▲ <?= htmlspecialchars($mv['asset']) ?> <?= sprintf('%+.1f%%', $mv['pct']*100) ?></div>
                                        <?php endforeach; ?>
                                        <?php foreach ($movers['down'] as $mv): ?>
                                            <div>▼ <?= htmlspecialchars($mv['asset']) ?> <?= sprintf('%+.1f%%', $mv['pct']*100) ?></div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="analysis-value-main">—</div>
                                    <div class="analysis-value-label">Pas assez de données</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="analysis-card fade-in pro-card">
                        <div class="analysis-card-header">
                            <h3>Top 3 — À acheter aujourd’hui</h3>
                            <?php if ($plan !== 'pro'): ?><span class="analysis-badge insuffisant">Pro</span><?php endif; ?>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div id="top-invest-list" class="pick-list <?= ($plan === 'starter' ? 'blur-value' : '') ?>">
                                    <?php foreach ($topWins as $it): ?>
                                        <div class="pick-item">
                                            <?php if (!empty($it['image'])): ?><img src="<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['symbol']) ?>" width="18" height="18"><?php endif; ?>
                                            <div class="pick-name"><?= htmlspecialchars($it['name']) ?> (<?= htmlspecialchars($it['symbol']) ?>)</div>
                                            <?php if ($it['price'] !== null): ?><div class="pick-price"><?= number_format($it['price'], 2, ',', ' ') ?> €</div><?php endif; ?>
                                            <div class="pick-change up"><?= sprintf('%+.1f%%', $it['chg']) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                        <?php if ($plan === 'starter'): ?>
                        <div class="pro-overlay" onclick="upgradePro()">
                            <a href="#" class="pro-button" onclick="upgradePro(); return false;">Passer en Pro</a>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div class="analysis-card fade-in pro-card">
                        <div class="analysis-card-header">
                            <h3>Top 3 — À vendre aujourd’hui</h3>
                            <?php if ($plan !== 'pro'): ?><span class="analysis-badge insuffisant">Pro</span><?php endif; ?>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div id="top-avoid-list" class="pick-list <?= ($plan === 'starter' ? 'blur-value' : '') ?>">
                                    <?php foreach ($topLosses as $it): ?>
                                        <div class="pick-item">
                                            <?php if (!empty($it['image'])): ?><img src="<?= htmlspecialchars($it['image']) ?>" alt="<?= htmlspecialchars($it['symbol']) ?>" width="18" height="18"><?php endif; ?>
                                            <div class="pick-name"><?= htmlspecialchars($it['name']) ?> (<?= htmlspecialchars($it['symbol']) ?>)</div>
                                            <?php if ($it['price'] !== null): ?><div class="pick-price"><?= number_format($it['price'], 2, ',', ' ') ?> €</div><?php endif; ?>
                                            <div class="pick-change down"><?= sprintf('%+.1f%%', $it['chg']) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                        <?php if ($plan === 'starter'): ?>
                        <div class="pro-overlay" onclick="upgradePro()">
                            <a href="#" class="pro-button" onclick="upgradePro(); return false;">Passer en Pro</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

            <section class="analysis-section">
                <div class="analysis-cards-grid">
                    <div class="analysis-card fade-in full-width pro-card">
                        <div class="analysis-card-header">
                            <h3>Heatmap du marché — Top 12</h3>
                            <?php if ($plan !== 'pro'): ?><span class="analysis-badge insuffisant">Pro</span><?php endif; ?>
                        </div>
                        <div class="analysis-card-content">
                            <div id="market-heat-grid" class="heat-grid <?= ($plan === 'starter' ? 'blur-value' : '') ?>">
                                <?php foreach ($heatItems as $h): ?>
                                    <div class="heat-cell <?= ($h['chg'] >= 0 ? 'up' : 'down') ?>">
                                        <?php if (!empty($h['image'])): ?><img src="<?= htmlspecialchars($h['image']) ?>" alt="<?= htmlspecialchars($h['symbol']) ?>" width="20" height="20"><?php endif; ?>
                                        <div class="heat-symbol"><?= htmlspecialchars($h['symbol']) ?></div>
                                        <div class="heat-change"><?= sprintf('%+.1f%%', (float)$h['chg']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php if ($plan === 'starter'): ?>
                        <div class="pro-overlay" onclick="upgradePro()">
                            <a href="#" class="pro-button" onclick="upgradePro(); return false;">Passer en Pro</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>

        </main>

    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeNavigation();
    initializeWealthSimulator();
    window.upgradePro = function(){
        window.location.href = 'https://checkout.stripe.com/c/pay/cs_test_a1keQIN23XdfenEHYZ5xKZFlj2JFpEgdzz8PZt7USyChGB4DxR5KwEECbo#fidnandhYHdWcXxpYCc%2FJ2FgY2RwaXEnKSdkdWxOYHwnPyd1blpxYHZxWjA0VlVKT3UyYEpmdnJOd0Ngd1xmMWhWNGhWdXVfMV9Wd0BrQ2h9PGRcREo1NkFUM09hdXMya3MwQXFOYnBqMUxPbHFfUWZNfW1KZkRsNH1hXXNpbzRoVkgyNTVxbmxcQEp1SScpJ2N3amhWYHdzYHcnP3F3cGApJ2dkZm5id2pwa2FGamlqdyc%2FJyZjY2NjY2MnKSdpZHxqcHFRfHVgJz8ndmxrYmlgWmxxYGgnKSdga2RnaWBVaWRmYG1qaWFgd3YnP3F3cGB4JSUl';
    };

    fillProContentClient();
});

// Override upgradePro pour envoyer un POST vers subscription.php (plan=pro)
window.upgradePro = function(){
    try {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'http://hugo:8090/html/subscription.php';
        const planInput = document.createElement('input');
        planInput.type = 'hidden';
        planInput.name = 'plan';
        planInput.value = 'pro';
        form.appendChild(planInput);
        document.body.appendChild(form);
        form.submit();
    } catch (e) {
        window.location.href = 'http://hugo:8090/html/subscription.php?plan=pro';
    }
};


function initializeNavigation() {
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const currentPage = window.location.pathname.split('/').pop();

    sidebarItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.includes(currentPage)) {
            sidebarItems.forEach(si => si.classList.remove('is-active'));
            item.classList.add('is-active');
            item.setAttribute('aria-current', 'page');
        }
    });

    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(targetId);
            const isExpanded = this.getAttribute('aria-expanded') === 'true';

            dropdownToggles.forEach(t => {
                if (t !== this) {
                    t.setAttribute('aria-expanded', 'false');
                    const otherId = t.getAttribute('data-dropdown-toggle');
                    const otherDropdown = document.getElementById(otherId);
                    if (otherDropdown) {
                        otherDropdown.style.display = 'none';
                    }
                }
            });

            this.setAttribute('aria-expanded', !isExpanded);
            if (dropdown) {
                dropdown.style.display = isExpanded ? 'none' : 'block';
            }
        });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown]')) {
            dropdownToggles.forEach(toggle => {
                toggle.setAttribute('aria-expanded', 'false');
                const targetId = toggle.getAttribute('data-dropdown-toggle');
                const dropdown = document.getElementById(targetId);
                if (dropdown) {
                    dropdown.style.display = 'none';
                }
            });
        }
    });
}

function fillProContentClient(){
    var investEl = document.getElementById('top-invest-list');
    var avoidEl = document.getElementById('top-avoid-list');
    var heatEl = document.getElementById('market-heat-grid');

    if (!investEl && !avoidEl && !heatEl) return;

    if (investEl && !investEl.children.length){
        investEl.innerHTML = '<div class="pick-item"><div class="pick-name">Bitcoin (btc)</div><div class="pick-price">60 000.00 €</div><div class="pick-change up">+2.5%</div></div>'+
                             '<div class="pick-item"><div class="pick-name">Ethereum (eth)</div><div class="pick-price">3 200.00 €</div><div class="pick-change up">+1.8%</div></div>'+
                             '<div class="pick-item"><div class="pick-name">Solana (sol)</div><div class="pick-price">150.00 €</div><div class="pick-change up">+3.1%</div></div>';
    }
    if (avoidEl && !avoidEl.children.length){
        avoidEl.innerHTML = '<div class="pick-item"><div class="pick-name">Dogecoin (doge)</div><div class="pick-price">0.12 €</div><div class="pick-change down">-4.2%</div></div>'+
                            '<div class="pick-item"><div class="pick-name">XRP (xrp)</div><div class="pick-price">0.55 €</div><div class="pick-change down">-1.7%</div></div>'+
                            '<div class="pick-item"><div class="pick-name">Cardano (ada)</div><div class="pick-price">0.40 €</div><div class="pick-change down">-2.3%</div></div>';
    }
    if (heatEl && !heatEl.children.length){
        heatEl.innerHTML = '<div class="heat-cell up"><div class="heat-symbol">btc</div><div class="heat-change">+2.5%</div></div>'+
                           '<div class="heat-cell down"><div class="heat-symbol">doge</div><div class="heat-change">-4.2%</div></div>'+
                           '<div class="heat-cell up"><div class="heat-symbol">eth</div><div class="heat-change">+1.8%</div></div>'+
                           '<div class="heat-cell up"><div class="heat-symbol">sol</div><div class="heat-change">+3.1%</div></div>'+
                           '<div class="heat-cell down"><div class="heat-symbol">ada</div><div class="heat-change">-2.3%</div></div>'+
                           '<div class="heat-cell up"><div class="heat-symbol">link</div><div class="heat-change">+0.9%</div></div>'+
                           '<div class="heat-cell down"><div class="heat-symbol">avax</div><div class="heat-change">-0.7%</div></div>'+
                           '<div class="heat-cell up"><div class="heat-symbol">matic</div><div class="heat-change">+1.2%</div></div>'+
                           '<div class="heat-cell up"><div class="heat-symbol">arb</div><div class="heat-change">+0.5%</div></div>'+
                           '<div class="heat-cell down"><div class="heat-symbol">apt</div><div class="heat-change">-1.1%</div></div>'+
                           '<div class="heat-cell up"><div class="heat-symbol">bnb</div><div class="heat-change">+0.8%</div></div>'+
                           '<div class="heat-cell down"><div class="heat-symbol">ftm</div><div class="heat-change">-0.6%</div></div>';
    }

    var base = 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=eur&sparkline=false&price_change_percentage=24h';
    var reqWins = fetch(base + '&order=price_change_percentage_24h_desc&per_page=3&page=1');
    var reqLoss = fetch(base + '&order=price_change_percentage_24h_asc&per_page=3&page=1');
    var reqHeat = fetch('https://api.coingecko.com/api/v3/coins/markets?vs_currency=eur&order=market_cap_desc&per_page=12&page=1&sparkline=false&price_change_percentage=24h');

    Promise.all([reqWins, reqLoss, reqHeat])
    .then(function(rs){ return Promise.all(rs.map(function(r){ return r.json(); })); })
    .then(function(all){
        var wins = all[0] || [];
        var losses = all[1] || [];
        var heat = all[2] || [];

        if (investEl && !investEl.children.length && wins.length){
            investEl.innerHTML = wins.map(viewPickUp).join('');
        }
        if (avoidEl && !avoidEl.children.length && losses.length){
            avoidEl.innerHTML = losses.map(viewPickDown).join('');
        }
        if (heatEl && !heatEl.children.length && heat.length){
            heatEl.innerHTML = heat.map(viewHeatCell).join('');
        }
    })
    .catch(function(){ /* on garde le contenu d’échantillon */ });

    function viewPickUp(it){
        var chg = (it.price_change_percentage_24h || 0);
        var price = (it.current_price != null) ? (it.current_price.toFixed(2) + ' \u20AC') : '';
        return '<div class="pick-item">'+
               (it.image ? '<img src="'+it.image+'" alt="'+escapeHtml(it.symbol)+'" width="18" height="18">' : '')+
               '<div class="pick-name">'+escapeHtml(it.name)+' ('+escapeHtml(it.symbol)+')</div>'+
               (price ? '<div class="pick-price">'+price+'</div>' : '')+
               '<div class="pick-change up">'+formatPct(chg)+'</div>'+
               '</div>';
    }
    function viewPickDown(it){
        var chg = (it.price_change_percentage_24h || 0);
        var price = (it.current_price != null) ? (it.current_price.toFixed(2) + ' \u20AC') : '';
        return '<div class="pick-item">'+
               (it.image ? '<img src="'+it.image+'" alt="'+escapeHtml(it.symbol)+'" width="18" height="18">' : '')+
               '<div class="pick-name">'+escapeHtml(it.name)+' ('+escapeHtml(it.symbol)+')</div>'+
               (price ? '<div class="pick-price">'+price+'</div>' : '')+
               '<div class="pick-change down">'+formatPct(chg)+'</div>'+
               '</div>';
    }
    function viewHeatCell(it){
        var chg = (it.price_change_percentage_24h || 0);
        var cls = (chg >= 0) ? 'up' : 'down';
        return '<div class="heat-cell '+cls+'">'+
               (it.image ? '<img src="'+it.image+'" alt="'+escapeHtml(it.symbol)+'" width="20" height="20">' : '')+
               '<div class="heat-symbol">'+escapeHtml(it.symbol)+'</div>'+
               '<div class="heat-change">'+formatPct(chg)+'</div>'+
               '</div>';
    }
    function formatPct(v){ var s = Number(v).toFixed(1); return (v>=0?'+':'') + s + '%'; }
    function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[m]); }); }
}

 function initializeAnalysisCards() {
     const cards = document.querySelectorAll('.analysis-card');
     cards.forEach(card => {
         // Neutraliser toute animation JS: rendre visible sans transition
         card.style.opacity = '';
         card.style.transform = '';
         card.style.transition = 'none';
     });
 }

    periodButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            periodButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateSimulation();
        });
    });

    let inputTimeout;
    monthlyInput.addEventListener('input', function() {
        clearTimeout(inputTimeout);
        inputTimeout = setTimeout(() => {
            updateSimulation();
        }, 500);
    });

</script>

<script src="../js/pages/analyse.js"></script>
</body>

</html>