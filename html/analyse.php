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

                            <span class="sidebar-user-email"><?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></span>

                        </span>

                        <img class="icon icon-chevron" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/chevron-up.svg" alt="" loading="lazy">

                        <img class="icon icon-chevron icon-chevron-alt" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/chevron-down.svg" alt="" loading="lazy">

                        <span class="sr-only">Ouvrir le menu profil</span>

                    </button>

                    <div class="dropdown-menu top-start" id="profile-menu" role="menu">

                        <a class="dropdown-item" href="#profil" role="menuitem">

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
            <!-- Analysis Cards Section -->
            <section class="analysis-section">
                <div class="section-header">
                    <h1 class="section-title">Analyse de votre portefeuille</h1>
                    <p class="section-subtitle">Découvrez les performances et la répartition de vos investissements</p>
                </div>

                <div class="analysis-cards-grid">
                    <!-- Fees Analysis Card -->
                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Scanner de frais</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div class="analysis-value-main">Optimisé</div>
                                <div class="analysis-value-label">0.45% de frais annuels</div>
                            </div>
                        </div>
                    </div>

                    <!-- Passive Income Card -->
                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Revenus passifs</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div class="analysis-value-main">0.30 %</div>
                                <div class="analysis-value-label">Rendement annuel moyen</div>
                            </div>
                        </div>
                    </div>

                    <!-- Sectoral Diversification Card -->
                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Scanner de diversification sectorielle</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div class="analysis-value-main">Modéré</div>
                                <div class="analysis-value-label">7 secteurs représentés</div>
                            </div>
                        </div>
                    </div>

                    <!-- Geographic Diversification Card -->
                    <div class="analysis-card fade-in">
                        <div class="analysis-card-header">
                            <h3>Scanner de diversification géographique</h3>
                        </div>
                        <div class="analysis-card-content">
                            <div class="analysis-value">
                                <div class="analysis-value-main">Insuffisant</div>
                                <div class="analysis-value-label">Concentration Europe: 85%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Wealth Simulator Section -->
            <section class="content-section">
                <article class="wealth-simulator">
                    <div class="simulator-header">
                        <h2 class="simulator-title">
                            Simulateur de patrimoine
                        </h2>
                    </div>

                    <div class="simulator-content">
                        <!-- Chart Section -->
                        <div class="simulator-chart-section">
                            <div class="chart-container">
                                <svg class="chart-svg" id="wealthChart" viewBox="0 0 900 400">
                                    <!-- Définitions -->
                                    <defs>
                                        <!-- Grille subtile -->
                                        <pattern id="subtleGrid" width="50" height="25" patternUnits="userSpaceOnUse">
                                            <path d="M 50 0 L 0 0 0 25" fill="none" stroke="rgba(255,255,255,0.03)" stroke-width="0.5"/>
                                        </pattern>
                                        
                                        <!-- Gradient pour la courbe principale -->
                                        <linearGradient id="mainCurveGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" style="stop-color:#D4AF37;stop-opacity:0.9"/>
                                            <stop offset="50%" style="stop-color:#FFD700;stop-opacity:1"/>
                                            <stop offset="100%" style="stop-color:#FFA500;stop-opacity:1"/>
                                        </linearGradient>
                                        
                                        <!-- Gradient pour l'aire sous la courbe -->
                                        <linearGradient id="areaGradient" x1="0%" y1="0%" x2="0%" y2="100%">
                                            <stop offset="0%" style="stop-color:#FFD700;stop-opacity:0.2"/>
                                            <stop offset="100%" style="stop-color:#FFD700;stop-opacity:0.05"/>
                                        </linearGradient>
                                    </defs>
                                    
                                    <!-- Fond avec grille subtile et effet de profondeur -->
                                    <rect x="80" y="20" width="750" height="300" fill="url(#subtleGrid)" />
                                    <rect x="80" y="20" width="750" height="300" fill="rgba(0,0,0,0.1)" />
                                    
                                    <!-- Axes principaux avec style amélioré -->
                                    <line x1="80" y1="320" x2="830" y2="320" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
                                    <line x1="80" y1="20" x2="80" y2="320" stroke="rgba(255,255,255,0.15)" stroke-width="1.5"/>
                                    
                                    <!-- Axe Y (Montants) - Style minimaliste amélioré -->
                                    <g class="y-axis" id="yAxis">
                                        <text x="70" y="325" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="end" font-family="'Segoe UI', sans-serif">0 €</text>
                                        <text x="70" y="275" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="end" font-family="'Segoe UI', sans-serif">400k €</text>
                                        <text x="70" y="225" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="end" font-family="'Segoe UI', sans-serif">800k €</text>
                                        <text x="70" y="175" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="end" font-family="'Segoe UI', sans-serif">1.2M €</text>
                                        <text x="70" y="125" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="end" font-family="'Segoe UI', sans-serif">1.6M €</text>
                                        <text x="70" y="75" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="end" font-family="'Segoe UI', sans-serif">2.0M €</text>
                                    </g>
                                    
                                    <!-- Lignes de référence horizontales très subtiles -->
                                    <g class="reference-lines">
                                        <line x1="80" y1="275" x2="830" y2="275" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                        <line x1="80" y1="225" x2="830" y2="225" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                        <line x1="80" y1="175" x2="830" y2="175" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                        <line x1="80" y1="125" x2="830" y2="125" stroke="rgba(255,255,255,0.03)" stroke-width="1"/>
                                    </g>
                                    
                                    <!-- Zone remplie sous la courbe avec gradient amélioré -->
                                    <path id="projection-area" d="M80,320 L830,80 L830,320 Z" fill="url(#areaGradient)" opacity="0.4"/>
                                    
                                    <!-- Courbe principale - Style premium similaire à l'image -->
                                    <path id="projection-main" d="M80,320 C200,315 350,280 500,200 C650,120 750,90 830,80" 
                                          stroke="url(#mainCurveGradient)" stroke-width="2.8" fill="none" opacity="1" stroke-linecap="round"/>
                                    
                                    <!-- Point de départ avec effet subtil -->
                                    <circle cx="80" cy="320" r="2.5" fill="#D4AF37" opacity="0.9"/>
                                    
                                    <!-- Point final avec animation douce -->
                                    <circle cx="830" cy="80" r="3.5" fill="#FFD700" opacity="1">
                                        <animate attributeName="r" values="3.5;4.5;3.5" dur="3s" repeatCount="indefinite"/>
                                        <animate attributeName="opacity" values="1;0.8;1" dur="3s" repeatCount="indefinite"/>
                                    </circle>
                                    
                                    <!-- Axe temporel (X) - Style minimaliste amélioré -->
                                    <g class="time-axis" id="timeAxis">
                                        <text x="80" y="345" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="middle" font-family="'Segoe UI', sans-serif">2024</text>
                                        <text x="230" y="345" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="middle" font-family="'Segoe UI', sans-serif">2030</text>
                                        <text x="380" y="345" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="middle" font-family="'Segoe UI', sans-serif">2035</text>
                                        <text x="530" y="345" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="middle" font-family="'Segoe UI', sans-serif">2040</text>
                                        <text x="680" y="345" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="middle" font-family="'Segoe UI', sans-serif">2045</text>
                                        <text x="830" y="345" fill="rgba(255,255,255,0.5)" font-size="10" text-anchor="middle" font-family="'Segoe UI', sans-serif">2054</text>
                                    </g>
                                </svg>
                            </div>

                            <div class="chart-metrics">
                                <div class="metric-item">
                                    <div class="metric-value" id="futureValue">1 234 567 890 €</div>
                                    <div class="metric-label">Valeur future</div>
                                </div>
                                <div class="metric-item">
                                    <div class="metric-value" id="totalContribution">450 000 €</div>
                                    <div class="metric-label">Total versé</div>
                                </div>
                                <div class="metric-item">
                                    <div class="metric-value" id="capitalGains">784 567 890 €</div>
                                    <div class="metric-label">Plus-values</div>
                                </div>
                                <div class="metric-item">
                                    <div class="metric-value" id="annualReturn">7.2%</div>
                                    <div class="metric-label">Rendement annuel</div>
                                </div>
                            </div>
                        </div>

                        <!-- Configuration Panel -->
                        <div class="simulator-config">
                            <h3 class="config-title">Modifier votre simulation</h3>

                            <div class="config-group">
                                <label class="config-label" for="monthlyAmount">Versement mensuel</label>
                                <input type="number" id="monthlyAmount" class="config-input" value="250" min="0" step="10">
                            </div>

                            <div class="config-group">
                                <label class="config-label">Période</label>
                                <div class="period-buttons">
                                    <button class="period-btn" data-period="10">10 ans</button>
                                    <button class="period-btn active" data-period="20">20 ans</button>
                                    <button class="period-btn" data-period="30">30 ans</button>
                                </div>
                            </div>

                            <button class="update-btn" onclick="updateSimulation()">
                                Mettre à jour
                            </button>
                        </div>
                    </div>
                </article>
            </section>

        </main>

    </div>

</div>

<script>
// Enhanced Navigation and Interactions
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeNavigation();
    initializeAnalysisCards();
    initializeWealthSimulator();
});

// Navigation Management
function initializeNavigation() {
    const sidebarItems = document.querySelectorAll('.sidebar-item');
    const currentPage = window.location.pathname.split('/').pop();
    
    // Update active state based on current page
    sidebarItems.forEach(item => {
        const href = item.getAttribute('href');
        if (href && href.includes(currentPage)) {
            sidebarItems.forEach(si => si.classList.remove('is-active'));
            item.classList.add('is-active');
            item.setAttribute('aria-current', 'page');
        }
    });
    
    // Dropdown management
    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(targetId);
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            // Close other dropdowns
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
            
            // Toggle current dropdown
            this.setAttribute('aria-expanded', !isExpanded);
            if (dropdown) {
                dropdown.style.display = isExpanded ? 'none' : 'block';
            }
        });
    });
    
    // Close dropdowns when clicking outside
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

// Analysis Cards Animation and Interaction
 function initializeAnalysisCards() {
     const cards = document.querySelectorAll('.analysis-card');
     
     // Intersection Observer for staggered fade-in animation
     const observer = new IntersectionObserver((entries) => {
         entries.forEach((entry, index) => {
             if (entry.isIntersecting) {
                 setTimeout(() => {
                     entry.target.style.opacity = '1';
                     entry.target.style.transform = 'translateY(0)';
                 }, index * 150);
             }
         });
     }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
 
     cards.forEach(card => {
         card.style.opacity = '0';
         card.style.transform = 'translateY(30px)';
         card.style.transition = 'all 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
         observer.observe(card);
     });
 }

// Wealth Simulator Enhanced Functions
function initializeWealthSimulator() {
    const periodButtons = document.querySelectorAll('.period-btn');
    const monthlyInput = document.getElementById('monthlyAmount');
    const updateButton = document.querySelector('.update-btn');
    
    if (!monthlyInput || !periodButtons.length) return;
    
    // Period button interactions
    periodButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            periodButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            updateSimulation();
        });
    });

    // Input debouncing for smooth updates
    let inputTimeout;
    monthlyInput.addEventListener('input', function() {
        clearTimeout(inputTimeout);
        inputTimeout = setTimeout(() => {
            updateSimulation();
        }, 500);
    });
    
    // Update button
    if (updateButton) {
        updateButton.addEventListener('click', updateSimulation);
    }

    // Initial simulation
    updateSimulation();
}

// Enhanced Simulation Update with Monte Carlo
function updateSimulation() {
    const monthlyAmount = parseFloat(document.getElementById('monthlyAmount')?.value) || 250;
    const activePeriod = document.querySelector('.period-btn.active');
    const years = parseInt(activePeriod?.dataset.period) || 20;
    
    // Run Monte Carlo simulation with 5000 iterations
    const monteCarloResults = runMonteCarloSimulation(monthlyAmount, years, 5000);
    
    // Animate value updates with Monte Carlo results
    animateValue('futureValue', monteCarloResults.median.futureValue, formatCurrency);
    animateValue('totalContribution', monteCarloResults.median.totalContribution, formatCurrency);
    animateValue('capitalGains', monteCarloResults.median.capitalGains, formatCurrency);
    animateValue('annualReturn', '7.2', (val) => val + '%');

    // Update chart with three scenarios (pessimistic, median, optimistic)
    updateMonteCarloChart(monthlyAmount, years, monteCarloResults);
}

// Monte Carlo Simulation Engine
function runMonteCarloSimulation(monthlyAmount, years, iterations = 5000) {
    const results = [];
    
    // Historical market data parameters (based on real market analysis)
    const marketParams = {
        meanReturn: 0.072,        // 7.2% annual return (historical average)
        volatility: 0.16,         // 16% volatility
        inflation: 0.02,          // 2% inflation
        correlationFactor: 0.85   // Market correlation factor
    };
    
    // Run 5000 Monte Carlo simulations
    for (let i = 0; i < iterations; i++) {
        const simulation = runSingleSimulation(monthlyAmount, years, marketParams);
        results.push(simulation);
    }
    
    // Sort results by final value for percentile calculation
    results.sort((a, b) => a.futureValue - b.futureValue);
    
    // Calculate key percentiles (5th, 50th, 95th for 90% confidence interval)
    const pessimisticIndex = Math.floor(iterations * 0.05);  // 5th percentile
    const medianIndex = Math.floor(iterations * 0.50);       // 50th percentile (median)
    const optimisticIndex = Math.floor(iterations * 0.95);   // 95th percentile
    
    return {
        pessimistic: results[pessimisticIndex],
        median: results[medianIndex],
        optimistic: results[optimisticIndex],
        allResults: results,
        confidenceInterval: 90 // 90% of results fall between pessimistic and optimistic
    };
}

// Single Monte Carlo Simulation
function runSingleSimulation(monthlyAmount, years, marketParams) {
    const months = years * 12;
    let portfolioValue = 0;
    let totalContribution = 0;
    const monthlyPath = []; // Store monthly values for chart generation
    
    for (let month = 1; month <= months; month++) {
        // Add monthly contribution
        portfolioValue += monthlyAmount;
        totalContribution += monthlyAmount;
        
        // Generate random market return using Box-Muller transformation
        const randomReturn = generateRandomReturn(marketParams);
        
        // Apply monthly return to portfolio
        const monthlyReturn = randomReturn / 12;
        portfolioValue *= (1 + monthlyReturn);
        
        // Store value for path generation (every 6 months for performance)
        if (month % 6 === 0) {
            monthlyPath.push({
                month: month,
                value: portfolioValue
            });
        }
    }
    
    // Adjust for inflation (net returns)
    const inflationAdjustment = Math.pow(1 + marketParams.inflation, years);
    const realValue = portfolioValue / inflationAdjustment;
    
    const capitalGains = realValue - totalContribution;
    
    return {
        futureValue: Math.round(realValue),
        totalContribution: Math.round(totalContribution),
        capitalGains: Math.round(capitalGains),
        monthlyPath: monthlyPath,
        nominalValue: Math.round(portfolioValue)
    };
}

// Generate Random Market Return using Box-Muller transformation
function generateRandomReturn(marketParams) {
    // Box-Muller transformation for normal distribution
    let u1 = 0, u2 = 0;
    while (u1 === 0) u1 = Math.random(); // Converting [0,1) to (0,1)
    while (u2 === 0) u2 = Math.random();
    
    const z0 = Math.sqrt(-2 * Math.log(u1)) * Math.cos(2 * Math.PI * u2);
    
    // Apply market parameters
    const randomReturn = marketParams.meanReturn + (z0 * marketParams.volatility);
    
    // Add some market correlation and bounds
    const boundedReturn = Math.max(-0.5, Math.min(0.8, randomReturn)); // Limit extreme values
    
    return boundedReturn;
}

// Enhanced Value Animation
function animateValue(elementId, targetValue, formatter) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const startValue = parseFloat(element.textContent.replace(/[^\d.-]/g, '')) || 0;
    const duration = 1500;
    const startTime = performance.now();

    function updateValue(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Enhanced easing function
        const easeOutCubic = 1 - Math.pow(1 - progress, 3);
        const currentValue = startValue + (targetValue - startValue) * easeOutCubic;
        
        element.textContent = formatter(currentValue);
        
        if (progress < 1) {
            requestAnimationFrame(updateValue);
        }
    }
    
    requestAnimationFrame(updateValue);
}

// Enhanced Currency Formatting
function formatCurrency(value) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value) + ' €';
}

// Enhanced Monte Carlo Chart Update with Realistic Scaling
function updateMonteCarloChart(monthlyAmount, years, monteCarloResults) {
    const svg = document.getElementById('wealthChart');
    if (!svg) return;

    // Update Y-axis with realistic values based on projections
    updateYAxis(monteCarloResults);

    // Generate smooth curve based on median scenario
    const medianResult = monteCarloResults.median;
    if (medianResult) {
        generateSmoothCurve(medianResult.monthlyPath, years, monteCarloResults);
    }

    // Update time axis
    updateTimeAxis(svg, years);
}

// Update Y-Axis with Dynamic Scaling
function updateYAxis(monteCarloResults) {
    const yAxis = document.getElementById('yAxis');
    if (!yAxis) return;

    // Find the maximum value across all scenarios for proper scaling
    const maxValue = Math.max(
        monteCarloResults.pessimistic.futureValue,
        monteCarloResults.median.futureValue,
        monteCarloResults.optimistic.futureValue
    );

    // Calculate appropriate scale with better rounding logic
    let scale;
    if (maxValue <= 50000) {
        scale = Math.ceil(maxValue / 10000) * 10000; // Round to nearest 10k for smaller values
    } else if (maxValue <= 500000) {
        scale = Math.ceil(maxValue / 50000) * 50000; // Round to nearest 50k for medium values
    } else {
        scale = Math.ceil(maxValue / 100000) * 100000; // Round to nearest 100k for larger values
    }

    // Ensure minimum scale for visibility
    scale = Math.max(scale, 50000);
    
    const steps = 5;
    const stepValue = scale / steps;

    // Update Y-axis labels with actual calculated values
    const yLabels = yAxis.querySelectorAll('text');
    yLabels.forEach((label, index) => {
        const value = (steps - index) * stepValue;
        
        // Animate label update
        label.style.transition = 'opacity 0.3s ease';
        label.style.opacity = '0';
        
        setTimeout(() => {
            label.textContent = formatAxisCurrency(value);
            label.style.opacity = '0.6';
        }, 100 + index * 50);
    });

    // Store the scale for use in curve generation
    window.currentChartScale = scale;
}

// Format Currency for Axis Labels
function formatAxisCurrency(value) {
    if (value === 0) return '0 €';
    
    // Better formatting for different value ranges
    if (value >= 1000000) {
        const millions = value / 1000000;
        return `${millions % 1 === 0 ? millions.toFixed(0) : millions.toFixed(1)}M €`;
    }
    
    if (value >= 1000) {
        const thousands = value / 1000;
        return `${thousands % 1 === 0 ? thousands.toFixed(0) : thousands.toFixed(0)}k €`;
    }
    
    return `${Math.round(value)} €`;
}

// Generate smooth curve similar to the provided image
function generateSmoothCurve(monthlyPath, years, allResults) {
    if (!monthlyPath || monthlyPath.length < 2) return;
    
    // Chart dimensions matching the SVG structure
    const chartWidth = 750;
    const chartHeight = 300;
    const startX = 80;
    const startY = 320;
    
    // Use the dynamic scale calculated in updateYAxis
    const maxValue = window.currentChartScale || Math.max(
        allResults.pessimistic.futureValue,
        allResults.median.futureValue,
        allResults.optimistic.futureValue
    );
    
    // Create key points for a smooth exponential-like curve
    const totalMonths = years * 12;
    const keyPoints = [];
    
    // Sample key points to create natural curve progression
    const sampleIndices = [0, Math.floor(totalMonths * 0.2), Math.floor(totalMonths * 0.5), 
                          Math.floor(totalMonths * 0.8), totalMonths - 1];
    
    sampleIndices.forEach((monthIndex, i) => {
        const pathIndex = Math.min(monthIndex / 6, monthlyPath.length - 1); // monthlyPath stores every 6 months
        const value = monthlyPath[Math.floor(pathIndex)]?.value || 0;
        
        const x = startX + (monthIndex / totalMonths) * chartWidth;
        // Use the dynamic scale for proper Y positioning
        const y = startY - (value / maxValue) * chartHeight;
        keyPoints.push({ x: Math.round(x), y: Math.round(y), value });
    });
    
    // Create smooth cubic bezier curve
    let pathData = `M${keyPoints[0].x},${keyPoints[0].y}`;
    
    for (let i = 1; i < keyPoints.length; i++) {
        const prev = keyPoints[i - 1];
        const curr = keyPoints[i];
        
        // Control points for smooth exponential-like progression
        const cp1x = prev.x + (curr.x - prev.x) * 0.4;
        const cp1y = prev.y;
        const cp2x = curr.x - (curr.x - prev.x) * 0.4;
        const cp2y = curr.y;
        
        pathData += ` C${cp1x},${cp1y} ${cp2x},${cp2y} ${curr.x},${curr.y}`;
    }
    
    // Update the main curve path with smooth animation
     const mainCurve = document.getElementById('projection-main');
     if (mainCurve) {
         mainCurve.setAttribute('d', pathData);
         
         // Smooth drawing animation
         const length = mainCurve.getTotalLength();
         mainCurve.style.strokeDasharray = length;
         mainCurve.style.strokeDashoffset = length;
         
         // Trigger animation
         setTimeout(() => {
             mainCurve.style.transition = 'stroke-dashoffset 2.5s cubic-bezier(0.4, 0, 0.2, 1)';
             mainCurve.style.strokeDashoffset = '0';
         }, 100);
         
         // Remove dash array after animation
         setTimeout(() => {
             mainCurve.style.strokeDasharray = 'none';
             mainCurve.style.transition = 'all 0.3s ease';
         }, 2600);
     }
    
    // Update the area under the curve
    const area = document.getElementById('projection-area');
    if (area) {
        const lastPoint = keyPoints[keyPoints.length - 1];
        const areaPath = pathData + ` L${lastPoint.x},${startY} L${startX},${startY} Z`;
        area.setAttribute('d', areaPath);
    }
    
    // Update end point position
    const endPoint = document.querySelector('circle[cx="830"]');
    if (endPoint && keyPoints.length > 0) {
        const lastPoint = keyPoints[keyPoints.length - 1];
        endPoint.setAttribute('cx', lastPoint.x);
        endPoint.setAttribute('cy', lastPoint.y);
    }
}

// Generate SVG Path for Projection
function generateProjectionPath(monthlyAmount, years, rate) {
    const points = [];
    const width = 700;
    const height = 150;
    const startX = 50;
    const startY = 250;

    // Generate smooth curve points
    for (let year = 0; year <= years; year += 0.5) {
        const projection = calculateProjection(monthlyAmount, year, rate);
        const maxValue = calculateProjection(monthlyAmount, years, 0.12).futureValue;
        
        const x = startX + (year / years) * width;
        const y = startY - (projection.futureValue / maxValue) * height;
        
        points.push({ x, y });
    }

    // Create smooth curve using quadratic bezier
    if (points.length < 2) return '';
    
    let path = `M${points[0].x},${points[0].y}`;
    
    for (let i = 1; i < points.length - 1; i++) {
        const curr = points[i];
        const next = points[i + 1];
        const cpx = (curr.x + next.x) / 2;
        const cpy = (curr.y + next.y) / 2;
        
        path += ` Q${curr.x},${curr.y} ${cpx},${cpy}`;
    }
    
    // Add final point
    const lastPoint = points[points.length - 1];
    path += ` T${lastPoint.x},${lastPoint.y}`;
    
    return path;
}

// Update Time Axis Labels with Realistic Positioning
function updateTimeAxis(svg, years) {
    const timeAxis = document.getElementById('timeAxis');
    if (!timeAxis) return;
    
    const timeLabels = timeAxis.querySelectorAll('text');
    const currentYear = new Date().getFullYear();
    
    // Calculate year positions based on chart width
    const positions = [80, 230, 380, 530, 680, 830]; // X positions matching the SVG
    
    // Calculer les années avec une progression plus réaliste
    const timePoints = [
        currentYear,
        currentYear + Math.floor(years * 0.2),
        currentYear + Math.floor(years * 0.4),
        currentYear + Math.floor(years * 0.6),
        currentYear + Math.floor(years * 0.8),
        currentYear + years
    ];
    
    timeLabels.forEach((label, index) => {
        if (index < positions.length && index < timePoints.length) {
            // Update position to match new chart structure
            label.setAttribute('x', positions[index]);
            
            // Animate year change
            label.style.transition = 'opacity 0.3s ease';
            label.style.opacity = '0';
            
            setTimeout(() => {
                label.textContent = timePoints[index].toString();
                label.style.opacity = '0.6';
            }, 150 + index * 50);
        }
    });
}
</script>

</body>

</html>