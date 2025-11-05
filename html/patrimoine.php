<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth.php");
    exit;
}

$username = trim((string)($_SESSION['username'] ?? ''));
if ($username === '') {
    $username = 'Compte Nova';
}

$userEmail = trim((string)($_SESSION['user_email'] ?? ''));
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

            <a href="app.php" title="accueil"><img src="../asset/img/logo.png" alt="Nova" class="navbar-logo"></a>

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

            <div class="dropdown" data-dropdown>

                <button class="icon-button avatar-button" type="button" aria-haspopup="menu" aria-expanded="false" data-dropdown-toggle="navbar-user-menu">

                    <span class="avatar avatar-square" aria-hidden="true">👤</span>

                    <span class="sr-only">Ouvrir le menu utilisateur</span>

                </button>

                <div class="dropdown-menu bottom-end" id="navbar-user-menu" role="menu">

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

                    <a class="sidebar-item" href="#reglages">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/cog-6-tooth.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Réglages</span>

                    </a>

                    <a class="sidebar-item" href="#diffusions">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/megaphone.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Diffusions</span>

                    </a>

                </div>



                <div class="sidebar-spacer" aria-hidden="true"></div>



                <div class="sidebar-section">

                    <a class="sidebar-item" href="#support">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/question-mark-circle.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Support</span>

                    </a>

                    <a class="sidebar-item" href="#changelog">

                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/sparkles.svg" alt="" loading="lazy">

                        <span class="sidebar-label">Changelog</span>

                    </a>

                </div>

            </div>



            <div class="sidebar-footer only-desktop">

                <div class="dropdown full-width" data-dropdown>

                    <button class="sidebar-item sidebar-button" type="button" aria-haspopup="menu" aria-expanded="false" data-dropdown-toggle="profile-menu">

                        <span class="avatar avatar-square avatar-large" aria-hidden="true">👤</span>

                        <span class="sidebar-label sidebar-user">
                            <span class="sidebar-user-name"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></span>
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

                        <a class="dropdown-item" href="#parametres" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/cog-8-tooth.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Paramètres</span>

                        </a>

                        <div class="dropdown-divider" role="none"></div>

                        <a class="dropdown-item" href="#confidentialite" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/shield-check.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Politique de confidentialité</span>

                        </a>

                        <a class="dropdown-item" href="#feedback" role="menuitem">

                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/light-bulb.svg" alt="" loading="lazy">

                            <span class="dropdown-label">Partager un feedback</span>

                        </a>

                        <div class="dropdown-divider" role="none"></div>

                        <a class="dropdown-item" href="#logout" role="menuitem">

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

                        </header>

                        <div class="performance-metric">

                            <span class="metric-label">Plus-value &middot; Tout</span>

                            <span class="metric-value">+680&nbsp;251&nbsp;724&nbsp;EUR</span>
                            <span class="metric-variation">+118,64&nbsp;%</span>

                        </div>

                        <p class="performance-description">

                            La plus-value latente est la difference entre votre prix d'achat unitaire et le prix actuel.
                            Ce montant ne tient pas compte des plus-values realisees.
                        </p>

                        <a class="performance-link" href="#details-performance">

                            En savoir plus

                            <svg width="16" height="16" viewBox="0 0 24 24" aria-hidden="true">

                                <path d="M9 18l6-6-6-6" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>

                            </svg>

                        </a>

                    </article>

                </div>

            </section>

            <section class="content-section">
                <div class="assets-section">
                    <div class="assets-header">
                        <div class="assets-header-text">
                            <h2>Actifs</h2>
                            <p>Suivez la repartition de votre patrimoine par classe d'actifs.</p>
                        </div>
                    </div>
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
                            <div class="assets-chart-toolbar" role="group" aria-label="Filtres patrimoine">
                                <button class="assets-filter-button is-active" type="button">Toutes les categories</button>
                                <button class="assets-filter-button" type="button">Tout</button>
                            </div>
                            <div class="assets-donut-wrapper">
                                <div class="assets-donut-ring" id="assets-donut-ring" role="img" aria-label="Diagramme des actifs"></div>
                                <div class="assets-donut-center">
                                    <span class="assets-donut-value" id="assets-total-value">--</span>
                                    <span class="assets-donut-label">Total</span>
                                </div>
                            </div>
                            <p class="assets-chart-note">Mise a jour selon vos positions synchronisees.</p>
                        </div>
                    </div>
                </div>
            </section>

        </main>

</div>

</div>



<script src="https://unpkg.com/lightweight-charts@4.1.1/dist/lightweight-charts.standalone.production.js"></script>

<script>

document.addEventListener('DOMContentLoaded', () => {

    const dropdowns = Array.from(document.querySelectorAll('[data-dropdown]'));



    const closeAll = (except) => {

        dropdowns.forEach(dropdown => {

            if (dropdown === except) {

                return;

            }

            const button = dropdown.querySelector('[data-dropdown-toggle]');

            const menuId = button ? button.getAttribute('data-dropdown-toggle') : null;

            const menu = menuId ? document.getElementById(menuId) : null;

            if (button) {

                button.setAttribute('aria-expanded', 'false');

            }

            if (menu) {

                menu.classList.remove('is-open');

            }

        });

    };



    dropdowns.forEach(dropdown => {

        const button = dropdown.querySelector('[data-dropdown-toggle]');

        if (!button) return;

        const menu = document.getElementById(button.getAttribute('data-dropdown-toggle'));

        if (!menu) return;



        button.addEventListener('click', event => {

            event.preventDefault();

            const isExpanded = button.getAttribute('aria-expanded') === 'true';

            closeAll(isExpanded ? null : dropdown);

            if (!isExpanded) {

                button.setAttribute('aria-expanded', 'true');

                menu.classList.add('is-open');

            }

        });



        button.addEventListener('keydown', event => {

            if (event.key === 'ArrowDown') {

                event.preventDefault();

                button.setAttribute('aria-expanded', 'true');

                menu.classList.add('is-open');

                const firstItem = menu.querySelector('[role="menuitem"]');

                if (firstItem) {

                    firstItem.focus();

                }

            }

        });



        menu.addEventListener('keydown', event => {

            if (event.key === 'Escape') {

                closeAll();

                button.focus();

            }

        });

    });




    document.addEventListener('click', event => {

        const container = event.target.closest('[data-dropdown]');

        if (!container) {

            closeAll();

        }

    });



    document.addEventListener('keydown', event => {

        if (event.key === 'Escape') {

            closeAll();

        }

    });



    const initRangeChart = () => {

        const chartContainer = document.getElementById('chart-range-container');

        if (!chartContainer || !window.LightweightCharts) {

            return;

        }



        const { createChart } = window.LightweightCharts;



        const CHART_HEIGHT = 320;



        const chartHost = document.createElement('div');

        chartHost.className = 'chart-host';

        chartHost.style.width = '100%';

        chartHost.style.height = `${CHART_HEIGHT}px`;

        chartContainer.appendChild(chartHost);



        const chartOptions = {

            layout: {

                textColor: 'black',

                background: { type: 'solid', color: 'white' },

            },

            height: CHART_HEIGHT,

        };



        const chart = createChart(chartHost, chartOptions);



        const dayData = [

            { time: '2018-10-19', value: 26.19 },

            { time: '2018-10-22', value: 25.87 },

            { time: '2018-10-23', value: 25.83 },

            { time: '2018-10-24', value: 25.78 },

            { time: '2018-10-25', value: 25.82 },

            { time: '2018-10-26', value: 25.81 },

            { time: '2018-10-29', value: 25.82 },

            { time: '2018-10-30', value: 25.71 },

            { time: '2018-10-31', value: 25.82 },

            { time: '2018-11-01', value: 25.72 },

            { time: '2018-11-02', value: 25.74 },

            { time: '2018-11-05', value: 25.81 },

            { time: '2018-11-06', value: 25.75 },

            { time: '2018-11-07', value: 25.73 },

            { time: '2018-11-08', value: 25.75 },

            { time: '2018-11-09', value: 25.75 },

            { time: '2018-11-12', value: 25.76 },

            { time: '2018-11-13', value: 25.8 },

            { time: '2018-11-14', value: 25.77 },

            { time: '2018-11-15', value: 25.75 },

            { time: '2018-11-16', value: 25.75 },

            { time: '2018-11-19', value: 25.75 },

            { time: '2018-11-20', value: 25.72 },

            { time: '2018-11-21', value: 25.78 },

            { time: '2018-11-23', value: 25.72 },

            { time: '2018-11-26', value: 25.78 },

            { time: '2018-11-27', value: 25.85 },

            { time: '2018-11-28', value: 25.85 },

            { time: '2018-11-29', value: 25.55 },

            { time: '2018-11-30', value: 25.41 },

            { time: '2018-12-03', value: 25.41 },

            { time: '2018-12-04', value: 25.42 },

            { time: '2018-12-06', value: 25.33 },

            { time: '2018-12-07', value: 25.39 },

            { time: '2018-12-10', value: 25.32 },

            { time: '2018-12-11', value: 25.48 },

            { time: '2018-12-12', value: 25.39 },

            { time: '2018-12-13', value: 25.45 },

            { time: '2018-12-14', value: 25.52 },

            { time: '2018-12-17', value: 25.38 },

            { time: '2018-12-18', value: 25.36 },

            { time: '2018-12-19', value: 25.65 },

            { time: '2018-12-20', value: 25.7 },

            { time: '2018-12-21', value: 25.66 },

            { time: '2018-12-24', value: 25.66 },

            { time: '2018-12-26', value: 25.65 },

            { time: '2018-12-27', value: 25.66 },

            { time: '2018-12-28', value: 25.68 },

            { time: '2018-12-31', value: 25.77 },

            { time: '2019-01-02', value: 25.72 },

            { time: '2019-01-03', value: 25.69 },

            { time: '2019-01-04', value: 25.71 },

            { time: '2019-01-07', value: 25.72 },

            { time: '2019-01-08', value: 25.72 },

            { time: '2019-01-09', value: 25.66 },

            { time: '2019-01-10', value: 25.85 },

            { time: '2019-01-11', value: 25.92 },

            { time: '2019-01-14', value: 25.94 },

            { time: '2019-01-15', value: 25.95 },

            { time: '2019-01-16', value: 26.0 },

            { time: '2019-01-17', value: 25.99 },

            { time: '2019-01-18', value: 25.6 },

            { time: '2019-01-22', value: 25.81 },

            { time: '2019-01-23', value: 25.7 },

            { time: '2019-01-24', value: 25.74 },

            { time: '2019-01-25', value: 25.8 },

            { time: '2019-01-28', value: 25.83 },

            { time: '2019-01-29', value: 25.7 },

            { time: '2019-01-30', value: 25.78 },

            { time: '2019-01-31', value: 25.35 },

            { time: '2019-02-01', value: 25.6 },

            { time: '2019-02-04', value: 25.65 },

            { time: '2019-02-05', value: 25.73 },

            { time: '2019-02-06', value: 25.71 },

            { time: '2019-02-07', value: 25.71 },

            { time: '2019-02-08', value: 25.72 },

            { time: '2019-02-11', value: 25.76 },

            { time: '2019-02-12', value: 25.84 },

            { time: '2019-02-13', value: 25.85 },

            { time: '2019-02-14', value: 25.87 },

            { time: '2019-02-15', value: 25.89 },

            { time: '2019-02-19', value: 25.9 },

            { time: '2019-02-20', value: 25.92 },

            { time: '2019-02-21', value: 25.96 },

            { time: '2019-02-22', value: 26.0 },

            { time: '2019-02-25', value: 25.93 },

            { time: '2019-02-26', value: 25.92 },

            { time: '2019-02-27', value: 25.67 },

            { time: '2019-02-28', value: 25.79 },

            { time: '2019-03-01', value: 25.86 },

            { time: '2019-03-04', value: 25.94 },

            { time: '2019-03-05', value: 26.02 },

            { time: '2019-03-06', value: 25.95 },

            { time: '2019-03-07', value: 25.89 },

            { time: '2019-03-08', value: 25.94 },

            { time: '2019-03-11', value: 25.91 },

            { time: '2019-03-12', value: 25.92 },

            { time: '2019-03-13', value: 26.0 },

            { time: '2019-03-14', value: 26.05 },

            { time: '2019-03-15', value: 26.11 },

            { time: '2019-03-18', value: 26.1 },

            { time: '2019-03-19', value: 25.98 },

            { time: '2019-03-20', value: 26.11 },

            { time: '2019-03-21', value: 26.12 },

            { time: '2019-03-22', value: 25.88 },

            { time: '2019-03-25', value: 25.85 },

            { time: '2019-03-26', value: 25.72 },

            { time: '2019-03-27', value: 25.73 },

            { time: '2019-03-28', value: 25.8 },

            { time: '2019-03-29', value: 25.77 },

            { time: '2019-04-01', value: 26.06 },

            { time: '2019-04-02', value: 25.93 },

            { time: '2019-04-03', value: 25.95 },

            { time: '2019-04-04', value: 26.06 },

            { time: '2019-04-05', value: 26.16 },

            { time: '2019-04-08', value: 26.12 },

            { time: '2019-04-09', value: 26.07 },

            { time: '2019-04-10', value: 26.13 },

            { time: '2019-04-11', value: 26.04 },

            { time: '2019-04-12', value: 26.04 },

            { time: '2019-04-15', value: 26.05 },

            { time: '2019-04-16', value: 26.01 },

            { time: '2019-04-17', value: 26.09 },

            { time: '2019-04-18', value: 26.0 },

            { time: '2019-04-22', value: 26.0 },

            { time: '2019-04-23', value: 26.06 },

            { time: '2019-04-24', value: 26.0 },

            { time: '2019-04-25', value: 25.81 },

            { time: '2019-04-26', value: 25.88 },

            { time: '2019-04-29', value: 25.91 },

            { time: '2019-04-30', value: 25.9 },

            { time: '2019-05-01', value: 26.02 },

            { time: '2019-05-02', value: 25.97 },

            { time: '2019-05-03', value: 26.02 },

            { time: '2019-05-06', value: 26.03 },

            { time: '2019-05-07', value: 26.04 },

            { time: '2019-05-08', value: 26.05 },

            { time: '2019-05-09', value: 26.05 },

            { time: '2019-05-10', value: 26.08 },

            { time: '2019-05-13', value: 26.05 },

            { time: '2019-05-14', value: 26.01 },

            { time: '2019-05-15', value: 26.03 },

            { time: '2019-05-16', value: 26.14 },

            { time: '2019-05-17', value: 26.09 },

            { time: '2019-05-20', value: 26.01 },

            { time: '2019-05-21', value: 26.12 },

            { time: '2019-05-22', value: 26.15 },

            { time: '2019-05-23', value: 26.18 },

            { time: '2019-05-24', value: 26.16 },

            { time: '2019-05-28', value: 26.23 },

        ];



        const weekData = [

            { time: '2016-07-18', value: 26.1 },

            { time: '2016-07-25', value: 26.19 },

            { time: '2016-08-01', value: 26.24 },

            { time: '2016-08-08', value: 26.22 },

            { time: '2016-08-15', value: 25.98 },

            { time: '2016-08-22', value: 25.85 },

            { time: '2016-08-29', value: 25.98 },

            { time: '2016-09-05', value: 25.71 },

            { time: '2016-09-12', value: 25.84 },

            { time: '2016-09-19', value: 25.89 },

            { time: '2016-09-26', value: 25.65 },

            { time: '2016-10-03', value: 25.69 },

            { time: '2016-10-10', value: 25.67 },

            { time: '2016-10-17', value: 26.11 },

            { time: '2016-10-24', value: 25.8 },

            { time: '2016-10-31', value: 25.7 },

            { time: '2016-11-07', value: 25.4 },

            { time: '2016-11-14', value: 25.32 },

            { time: '2016-11-21', value: 25.48 },

            { time: '2016-11-28', value: 25.08 },

            { time: '2016-12-05', value: 25.06 },

            { time: '2016-12-12', value: 25.11 },

            { time: '2016-12-19', value: 25.34 },

            { time: '2016-12-26', value: 25.2 },

            { time: '2017-01-02', value: 25.33 },

            { time: '2017-01-09', value: 25.56 },

            { time: '2017-01-16', value: 25.32 },

            { time: '2017-01-23', value: 25.71 },

            { time: '2017-01-30', value: 25.85 },

            { time: '2017-02-06', value: 25.77 },

            { time: '2017-02-13', value: 25.94 },

            { time: '2017-02-20', value: 25.67 },

            { time: '2017-02-27', value: 25.6 },

            { time: '2017-03-06', value: 25.54 },

            { time: '2017-03-13', value: 25.84 },

            { time: '2017-03-20', value: 25.96 },

            { time: '2017-03-27', value: 25.9 },

            { time: '2017-04-03', value: 25.97 },

            { time: '2017-04-10', value: 26.0 },

            { time: '2017-04-17', value: 26.13 },

            { time: '2017-04-24', value: 26.02 },

            { time: '2017-05-01', value: 26.3 },

            { time: '2017-05-08', value: 26.27 },

            { time: '2017-05-15', value: 26.24 },

            { time: '2017-05-22', value: 26.02 },

            { time: '2017-05-29', value: 26.2 },

            { time: '2017-06-05', value: 26.12 },

            { time: '2017-06-12', value: 26.2 },

            { time: '2017-06-19', value: 26.46 },

            { time: '2017-06-26', value: 26.39 },

            { time: '2017-07-03', value: 26.52 },

            { time: '2017-07-10', value: 26.57 },

            { time: '2017-07-17', value: 26.65 },

            { time: '2017-07-24', value: 26.45 },

            { time: '2017-07-31', value: 26.37 },

            { time: '2017-08-07', value: 26.13 },

            { time: '2017-08-14', value: 26.21 },

            { time: '2017-08-21', value: 26.31 },

            { time: '2017-08-28', value: 26.33 },

            { time: '2017-09-04', value: 26.38 },

            { time: '2017-09-11', value: 26.38 },

            { time: '2017-09-18', value: 26.5 },

            { time: '2017-09-25', value: 26.39 },

            { time: '2017-10-02', value: 25.95 },

            { time: '2017-10-09', value: 26.15 },

            { time: '2017-10-16', value: 26.43 },

            { time: '2017-10-23', value: 26.22 },

            { time: '2017-10-30', value: 26.14 },

            { time: '2017-11-06', value: 26.08 },

            { time: '2017-11-13', value: 26.27 },

            { time: '2017-11-20', value: 26.3 },

            { time: '2017-11-27', value: 25.92 },

            { time: '2017-12-04', value: 26.1 },

            { time: '2017-12-11', value: 25.88 },

            { time: '2017-12-18', value: 25.82 },

            { time: '2017-12-25', value: 25.82 },

            { time: '2018-01-01', value: 25.81 },

            { time: '2018-01-08', value: 25.95 },

            { time: '2018-01-15', value: 26.03 },

            { time: '2018-01-22', value: 26.04 },

            { time: '2018-01-29', value: 25.96 },

            { time: '2018-02-05', value: 25.99 },

            { time: '2018-02-12', value: 26.0 },

            { time: '2018-02-19', value: 26.06 },

            { time: '2018-02-26', value: 25.77 },

            { time: '2018-03-05', value: 25.81 },

            { time: '2018-03-12', value: 25.88 },

            { time: '2018-03-19', value: 25.72 },

            { time: '2018-03-26', value: 25.75 },

            { time: '2018-04-02', value: 25.7 },

            { time: '2018-04-09', value: 25.73 },

            { time: '2018-04-16', value: 25.74 },

            { time: '2018-04-23', value: 25.69 },

            { time: '2018-04-30', value: 25.76 },

            { time: '2018-05-07', value: 25.89 },

            { time: '2018-05-14', value: 25.89 },

            { time: '2018-05-21', value: 26.0 },

            { time: '2018-05-28', value: 25.79 },

            { time: '2018-06-04', value: 26.11 },

            { time: '2018-06-11', value: 26.43 },

            { time: '2018-06-18', value: 26.3 },

            { time: '2018-06-25', value: 26.58 },

            { time: '2018-07-02', value: 26.33 },

            { time: '2018-07-09', value: 26.33 },

            { time: '2018-07-16', value: 26.32 },

            { time: '2018-07-23', value: 26.2 },

            { time: '2018-07-30', value: 26.03 },

            { time: '2018-08-06', value: 26.15 },

            { time: '2018-08-13', value: 26.17 },

            { time: '2018-08-20', value: 26.28 },

            { time: '2018-08-27', value: 25.86 },

            { time: '2018-09-03', value: 25.69 },

            { time: '2018-09-10', value: 25.69 },

            { time: '2018-09-17', value: 25.64 },

            { time: '2018-09-24', value: 25.67 },

            { time: '2018-10-01', value: 25.55 },

            { time: '2018-10-08', value: 25.59 },

            { time: '2018-10-15', value: 26.19 },

            { time: '2018-10-22', value: 25.81 },

            { time: '2018-10-29', value: 25.74 },

            { time: '2018-11-05', value: 25.75 },

            { time: '2018-11-12', value: 25.75 },

            { time: '2018-11-19', value: 25.72 },

            { time: '2018-11-26', value: 25.41 },

            { time: '2018-12-03', value: 25.39 },

            { time: '2018-12-10', value: 25.52 },

            { time: '2018-12-17', value: 25.66 },

            { time: '2018-12-24', value: 25.68 },

            { time: '2018-12-31', value: 25.71 },

            { time: '2019-01-07', value: 25.92 },

            { time: '2019-01-14', value: 25.6 },

            { time: '2019-01-21', value: 25.8 },

            { time: '2019-01-28', value: 25.6 },

            { time: '2019-02-04', value: 25.72 },

            { time: '2019-02-11', value: 25.89 },

            { time: '2019-02-18', value: 26.0 },

            { time: '2019-02-25', value: 25.86 },

            { time: '2019-03-04', value: 25.94 },

            { time: '2019-03-11', value: 26.11 },

            { time: '2019-03-18', value: 25.88 },

            { time: '2019-03-25', value: 25.77 },

            { time: '2019-04-01', value: 26.16 },

            { time: '2019-04-08', value: 26.04 },

            { time: '2019-04-15', value: 26.0 },

            { time: '2019-04-22', value: 25.88 },

            { time: '2019-04-29', value: 26.02 },

            { time: '2019-05-06', value: 26.08 },

            { time: '2019-05-13', value: 26.09 },

            { time: '2019-05-20', value: 26.16 },

            { time: '2019-05-27', value: 26.23 },

        ];



        const monthData = [

            { time: '2006-12-01', value: 25.4 },

            { time: '2007-01-01', value: 25.5 },

            { time: '2007-02-01', value: 25.11 },

            { time: '2007-03-01', value: 25.24 },

            { time: '2007-04-02', value: 25.34 },

            { time: '2007-05-01', value: 24.82 },

            { time: '2007-06-01', value: 23.85 },

            { time: '2007-07-02', value: 23.24 },

            { time: '2007-08-01', value: 23.05 },

            { time: '2007-09-03', value: 22.26 },

            { time: '2007-10-01', value: 22.52 },

            { time: '2007-11-01', value: 20.84 },

            { time: '2007-12-03', value: 20.37 },

            { time: '2008-01-01', value: 23.9 },

            { time: '2008-02-01', value: 22.58 },

            { time: '2008-03-03', value: 21.74 },

            { time: '2008-04-01', value: 22.5 },

            { time: '2008-05-01', value: 22.38 },

            { time: '2008-06-02', value: 20.58 },

            { time: '2008-07-01', value: 20.6 },

            { time: '2008-08-01', value: 20.82 },

            { time: '2008-09-01', value: 17.5 },

            { time: '2008-10-01', value: 17.7 },

            { time: '2008-11-03', value: 15.52 },

            { time: '2008-12-01', value: 18.58 },

            { time: '2009-01-01', value: 15.4 },

            { time: '2009-02-02', value: 11.68 },

            { time: '2009-03-02', value: 14.89 },

            { time: '2009-04-01', value: 16.24 },

            { time: '2009-05-01', value: 18.33 },

            { time: '2009-06-01', value: 18.08 },

            { time: '2009-07-01', value: 20.07 },

            { time: '2009-08-03', value: 20.35 },

            { time: '2009-09-01', value: 21.53 },

            { time: '2009-10-01', value: 21.48 },

            { time: '2009-11-02', value: 20.28 },

            { time: '2009-12-01', value: 21.39 },

            { time: '2010-01-01', value: 22.0 },

            { time: '2010-02-01', value: 22.31 },

            { time: '2010-03-01', value: 22.82 },

            { time: '2010-04-01', value: 22.58 },

            { time: '2010-05-03', value: 21.02 },

            { time: '2010-06-01', value: 21.45 },

            { time: '2010-07-01', value: 22.42 },

            { time: '2010-08-02', value: 23.61 },

            { time: '2010-09-01', value: 24.4 },

            { time: '2010-10-01', value: 24.46 },

            { time: '2010-11-01', value: 23.64 },

            { time: '2010-12-01', value: 22.9 },

            { time: '2011-01-03', value: 23.73 },

            { time: '2011-02-01', value: 23.52 },

            { time: '2011-03-01', value: 24.15 },

            { time: '2011-04-01', value: 24.37 },

            { time: '2011-05-02', value: 24.4 },

            { time: '2011-06-01', value: 24.45 },

            { time: '2011-07-01', value: 24.24 },

            { time: '2011-08-01', value: 24.0 },

            { time: '2011-09-01', value: 22.77 },

            { time: '2011-10-03', value: 24.21 },

            { time: '2011-11-01', value: 23.4 },

            { time: '2011-12-01', value: 23.9 },

            { time: '2012-01-02', value: 24.84 },

            { time: '2012-02-01', value: 25.04 },

            { time: '2012-03-01', value: 24.9 },

            { time: '2012-04-02', value: 25.06 },

            { time: '2012-05-01', value: 24.63 },

            { time: '2012-06-01', value: 25.07 },

            { time: '2012-07-02', value: 25.3 },

            { time: '2012-08-01', value: 25.08 },

            { time: '2012-09-03', value: 25.27 },

            { time: '2012-10-01', value: 25.39 },

            { time: '2012-11-01', value: 25.06 },

            { time: '2012-12-03', value: 25.03 },

            { time: '2013-01-01', value: 25.26 },

            { time: '2013-02-01', value: 25.2 },

            { time: '2013-03-01', value: 25.3 },

            { time: '2013-04-01', value: 25.38 },

            { time: '2013-05-01', value: 25.22 },

            { time: '2013-06-03', value: 24.88 },

            { time: '2013-07-01', value: 24.98 },

            { time: '2013-08-01', value: 24.6 },

            { time: '2013-09-02', value: 24.65 },

            { time: '2013-10-01', value: 24.62 },

            { time: '2013-11-01', value: 24.65 },

            { time: '2013-12-02', value: 24.7 },

            { time: '2014-01-01', value: 24.98 },

            { time: '2014-02-03', value: 24.95 },

            { time: '2014-03-03', value: 25.45 },

            { time: '2014-04-01', value: 25.4 },

            { time: '2014-05-01', value: 25.51 },

            { time: '2014-06-02', value: 25.34 },

            { time: '2014-07-01', value: 25.3 },

            { time: '2014-08-01', value: 25.36 },

            { time: '2014-09-01', value: 25.16 },

            { time: '2014-10-01', value: 25.53 },

            { time: '2014-11-03', value: 25.4 },

            { time: '2014-12-01', value: 25.7 },

            { time: '2015-01-01', value: 25.95 },

            { time: '2015-02-02', value: 25.81 },

            { time: '2015-03-02', value: 25.63 },

            { time: '2015-04-01', value: 25.39 },

            { time: '2015-05-01', value: 25.62 },

            { time: '2015-06-01', value: 25.23 },

            { time: '2015-07-01', value: 25.47 },

            { time: '2015-08-03', value: 25.18 },

            { time: '2015-09-01', value: 25.3 },

            { time: '2015-10-01', value: 25.68 },

            { time: '2015-11-02', value: 25.63 },

            { time: '2015-12-01', value: 25.57 },

            { time: '2016-01-01', value: 25.55 },

            { time: '2016-02-01', value: 25.05 },

            { time: '2016-03-01', value: 25.61 },

            { time: '2016-04-01', value: 25.91 },

            { time: '2016-05-02', value: 25.84 },

            { time: '2016-06-01', value: 25.94 },

            { time: '2016-07-01', value: 26.19 },

            { time: '2016-08-01', value: 26.06 },

            { time: '2016-09-01', value: 25.65 },

            { time: '2016-10-03', value: 25.8 },

            { time: '2016-11-01', value: 25.06 },

            { time: '2016-12-01', value: 25.2 },

            { time: '2017-01-02', value: 25.7 },

            { time: '2017-02-01', value: 25.78 },

            { time: '2017-03-01', value: 25.9 },

            { time: '2017-04-03', value: 26.02 },

            { time: '2017-05-01', value: 26.02 },

            { time: '2017-06-01', value: 26.39 },

            { time: '2017-07-03', value: 26.3 },

            { time: '2017-08-01', value: 26.14 },

            { time: '2017-09-01', value: 26.39 },

            { time: '2017-10-02', value: 26.12 },

            { time: '2017-11-01', value: 25.81 },

            { time: '2017-12-01', value: 25.82 },

            { time: '2018-01-01', value: 26.06 },

            { time: '2018-02-01', value: 25.78 },

            { time: '2018-03-01', value: 25.75 },

            { time: '2018-04-02', value: 25.72 },

            { time: '2018-05-01', value: 25.75 },

            { time: '2018-06-01', value: 26.58 },

            { time: '2018-07-02', value: 26.14 },

            { time: '2018-08-01', value: 25.86 },

            { time: '2018-09-03', value: 25.67 },

            { time: '2018-10-01', value: 25.82 },

            { time: '2018-11-01', value: 25.41 },

            { time: '2018-12-03', value: 25.77 },

            { time: '2019-01-01', value: 25.35 },

            { time: '2019-02-01', value: 25.79 },

            { time: '2019-03-01', value: 25.77 },

            { time: '2019-04-01', value: 25.9 },

            { time: '2019-05-01', value: 26.23 },

        ];



        const yearData = [

            { time: '2006-01-02', value: 24.89 },

            { time: '2007-01-01', value: 25.5 },

            { time: '2008-01-01', value: 23.9 },

            { time: '2009-01-01', value: 15.4 },

            { time: '2010-01-01', value: 22.0 },

            { time: '2011-01-03', value: 23.73 },

            { time: '2012-01-02', value: 24.84 },

            { time: '2013-01-01', value: 25.26 },

            { time: '2014-01-01', value: 24.98 },

            { time: '2015-01-01', value: 25.95 },

            { time: '2016-01-01', value: 25.55 },

            { time: '2017-01-02', value: 25.7 },

            { time: '2018-01-01', value: 26.06 },

            { time: '2019-01-01', value: 26.23 },

        ];



        const seriesData = new Map([

            ['1D', dayData],

            ['1W', weekData],

            ['1M', monthData],

            ['1Y', yearData],

        ]);



        const intervalColors = {

            '1D': '#2962FF',

            '1W': 'rgb(225, 87, 90)',

            '1M': 'rgb(242, 142, 44)',

            '1Y': 'rgb(164, 89, 209)',

        };



        const lineSeries = chart.addLineSeries({ color: intervalColors['1D'] });

        const buttons = [];



        const setChartInterval = (interval) => {

            const data = seriesData.get(interval);

            if (!data) {

                return;

            }

            lineSeries.setData(data);

            lineSeries.applyOptions({

                color: intervalColors[interval],

            });

            chart.timeScale().fitContent();

            buttons.forEach(button => {

                const isActive = button.dataset.interval === interval;

                button.classList.toggle('is-active', isActive);

                button.setAttribute('aria-pressed', isActive ? 'true' : 'false');

            });

        };



        const styles = `

            .buttons-container {

                display: flex;

                flex-direction: row;

                justify-content: flex-end;

                align-items: center;

                gap: 12px;

                flex-wrap: wrap;

                margin-top: 16px;

            }

            .buttons-container button {

                all: initial;

                font-family: -apple-system, BlinkMacSystemFont, 'Trebuchet MS', Roboto, Ubuntu, sans-serif;

                font-size: 16px;

                font-style: normal;

                font-weight: 510;

                line-height: 24px;

                letter-spacing: -0.32px;

                padding: 8px 24px;

                color: rgba(19, 23, 34, 1);

                background-color: rgba(240, 243, 250, 1);

                border-radius: 8px;

                cursor: pointer;

            }



            .buttons-container button:hover {

                background-color: rgba(224, 227, 235, 1);

            }



            .buttons-container button:active {

                background-color: rgba(209, 212, 220, 1);

            }



            .buttons-container button.is-active {

                background-color: rgba(41, 98, 255, 0.15);

                color: rgba(19, 23, 34, 1);

            }



            .buttons-container button:focus-visible {

                outline: 2px solid rgba(41, 98, 255, 0.35);

                outline-offset: 2px;

            }

        `;



        const stylesElement = document.createElement('style');

        stylesElement.textContent = styles;

        document.head.appendChild(stylesElement);



        const buttonsContainer = document.createElement('div');

        buttonsContainer.classList.add('buttons-container');

        const intervals = ['1D', '1W', '1M', '1Y'];

        intervals.forEach(interval => {

            const button = document.createElement('button');

            button.innerText = interval;

            button.type = 'button';

            button.addEventListener('click', () => setChartInterval(interval));

            button.dataset.interval = interval;

            button.setAttribute('aria-pressed', 'false');

            buttons.push(button);

            buttonsContainer.appendChild(button);

        });



        chartContainer.insertBefore(buttonsContainer, chartHost);

        setChartInterval('1D');



        const resizeChart = () => {

            const width = chartContainer.clientWidth || chartHost.clientWidth;

            chart.resize(width, CHART_HEIGHT);

        };



        window.addEventListener('resize', resizeChart);

        resizeChart();

    };



    initRangeChart();

    const initAssetsSection = () => {
        const list = document.getElementById('assets-list');
        const donut = document.getElementById('assets-donut-ring');
        const totalValueTarget = document.getElementById('assets-total-value');

        if (!list || !donut || !totalValueTarget) {
            return;
        }

        const assetsData = [
            { name: 'Actions & Fonds', share: 39, value: 538851384, deltaValue: 35740030, deltaPercent: 7.09, color: '#3b82f6' },
            { name: 'Crypto', share: 18, value: 221197933, deltaValue: 171060186, deltaPercent: 341.6, color: '#8b5cf6' },
            { name: 'Crowdlending', share: 15, value: 210000000, deltaValue: 0, deltaPercent: 0, color: '#06b6d4' },
        ];

        const numberFormatter = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        const percentFormatter = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        const formatShare = (value) => value.toLocaleString('fr-FR', {
            minimumFractionDigits: value < 1 ? 2 : 0,
            maximumFractionDigits: value < 1 ? 2 : 0,
        });

        const formatCurrency = (value) => `${numberFormatter.format(value)} €`;

        const formatSignedCurrency = (value) => {
            if (!value) {
                return '—';
            }
            const sign = value >= 0 ? '+' : '−';
            return `${sign}${numberFormatter.format(Math.abs(value))} €`;
        };

        const formatSignedPercent = (value) => {
            if (!value) {
                return '—';
            }
            const sign = value >= 0 ? '+' : '−';
            return `${sign}${percentFormatter.format(Math.abs(value))} %`;
        };

        const totalValue = assetsData.reduce((sum, asset) => sum + asset.value, 0);
        totalValueTarget.textContent = formatCurrency(totalValue);

        const fragment = document.createDocumentFragment();

        assetsData.forEach(asset => {
            const row = document.createElement('li');
            row.className = 'assets-row';
            const deltaClass = asset.deltaValue > 0 ? 'is-positive' : asset.deltaValue < 0 ? 'is-negative' : 'is-neutral';

            row.innerHTML = `
                <div class="assets-row-name">
                    <span class="assets-row-arrow" aria-hidden="true">
                        <svg width="14" height="14" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" />
                        </svg>
                    </span>
                    <span class="assets-row-dot" style="background:${asset.color};"></span>
                    <span class="assets-row-label">${asset.name}</span>
                </div>
                <span class="assets-row-share">${formatShare(asset.share)} %</span>
                <span class="assets-row-value">${formatCurrency(asset.value)}</span>
                <span class="assets-row-delta">
                    <span class="assets-delta-amount ${deltaClass}">${formatSignedCurrency(asset.deltaValue)}</span>
                    <span class="assets-delta-percent ${deltaClass}">${formatSignedPercent(asset.deltaPercent)}</span>
                </span>
            `;

            fragment.appendChild(row);
        });

        list.appendChild(fragment);

        const totalShare = assetsData.reduce((sum, asset) => sum + asset.share, 0);
        let start = 0;
        const gradientParts = assetsData.map(asset => {
            const ratio = asset.share / totalShare;
            const end = start + ratio;
            const part = `${asset.color} ${start * 100}% ${end * 100}%`;
            start = end;
            return part;
        });

        donut.style.background = `conic-gradient(${gradientParts.join(', ')})`;
    };

    initAssetsSection();
});

</script>

<script>
// Navigation sidebar améliorée
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des états actifs de la sidebar
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const currentPage = window.location.pathname.split('/').pop();
    
    // Mettre à jour l'état actif basé sur la page actuelle
    sidebarItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.includes(currentPage)) {
            // Retirer l'état actif de tous les éléments
            sidebarItems.forEach(si => si.classList.remove('is-active'));
            // Ajouter l'état actif à l'élément courant
            item.classList.add('is-active');
            item.setAttribute('aria-current', 'page');
        }
    });
    
    // Gestion des dropdowns
    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(targetId);
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Fermer tous les autres dropdowns
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
            
            // Toggle le dropdown courant
            this.setAttribute('aria-expanded', !isExpanded);
            if (dropdown) {
                dropdown.style.display = isExpanded ? 'none' : 'block';
            }
        });
    });
    
    // Fermer les dropdowns en cliquant ailleurs
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
});
</script>

</body>

</html>

