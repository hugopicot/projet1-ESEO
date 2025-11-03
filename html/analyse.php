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

            <div class="dropdown" data-dropdown>

                <button class="icon-button avatar-button" type="button" aria-haspopup="menu" aria-expanded="false" data-dropdown-toggle="navbar-user-menu">

                    <span class="avatar avatar-square" aria-hidden="true">ER</span>

                    <span class="sr-only">Ouvrir le menu utilisateur</span>

                </button>

                <div class="dropdown-menu bottom-end" id="navbar-user-menu" role="menu">

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

                        <span class="avatar avatar-square avatar-large" aria-hidden="true">ER</span>

                        <span class="sidebar-label sidebar-user">

                            <span class="sidebar-user-name">Erica</span>

                            <span class="sidebar-user-email">erica@example.com</span>

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
                            <span class="analysis-badge plus">PLUS</span>
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
                            <span class="analysis-badge plus">PLUS</span>
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
                            <span class="analysis-badge plus">PLUS</span>
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
                            <span class="analysis-badge plus">PLUS</span>
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
                            <span class="simulator-badge">PLUS</span>
                        </h2>
                    </div>

                    <div class="simulator-content">
                        <!-- Chart Section -->
                        <div class="simulator-chart-section">
                            <div class="chart-container">
                                <svg class="chart-svg" id="wealthChart" viewBox="0 0 800 300">
                                    <!-- Grille -->
                                    <defs>
                                        <pattern id="grid" width="40" height="30" patternUnits="userSpaceOnUse">
                                            <path d="M 40 0 L 0 0 0 30" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
                                        </pattern>
                                    </defs>
                                    <rect width="100%" height="100%" fill="url(#grid)" />
                                    
                                    <!-- Courbes de projection -->
                                    <path id="projection-conservative" d="M50,250 Q200,240 350,220 Q500,200 750,180" stroke="#4F46E5" stroke-width="2" fill="none"/>
                                    <path id="projection-moderate" d="M50,250 Q200,230 350,200 Q500,170 750,140" stroke="#7C3AED" stroke-width="2" fill="none"/>
                                    <path id="projection-aggressive" d="M50,250 Q200,220 350,180 Q500,140 750,100" stroke="#EC4899" stroke-width="2" fill="none"/>
                                    
                                    <!-- Axe temporel -->
                                    <g class="time-axis">
                                        <text x="50" y="290" fill="rgba(255,255,255,0.6)" font-size="12">2024</text>
                                        <text x="200" y="290" fill="rgba(255,255,255,0.6)" font-size="12">2030</text>
                                        <text x="350" y="290" fill="rgba(255,255,255,0.6)" font-size="12">2035</text>
                                        <text x="500" y="290" fill="rgba(255,255,255,0.6)" font-size="12">2040</text>
                                        <text x="650" y="290" fill="rgba(255,255,255,0.6)" font-size="12">2045</text>
                                        <text x="750" y="290" fill="rgba(255,255,255,0.6)" font-size="12">2054</text>
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

// Enhanced Simulation Update
function updateSimulation() {
    const monthlyAmount = parseFloat(document.getElementById('monthlyAmount')?.value) || 250;
    const activePeriod = document.querySelector('.period-btn.active');
    const years = parseInt(activePeriod?.dataset.period) || 20;
    
    // Calculate projections with different scenarios
    const scenarios = {
        conservative: 0.04,
        moderate: 0.072,
        aggressive: 0.10
    };

    const results = calculateProjection(monthlyAmount, years, scenarios.moderate);
    
    // Animate value updates with enhanced formatting
    animateValue('futureValue', results.futureValue, formatCurrency);
    animateValue('totalContribution', results.totalContribution, formatCurrency);
    animateValue('capitalGains', results.capitalGains, formatCurrency);
    animateValue('annualReturn', (scenarios.moderate * 100).toFixed(1), (val) => val + '%');

    // Update chart with smooth animations
    updateChart(monthlyAmount, years, scenarios);
}

// Calculate Financial Projection
function calculateProjection(monthlyAmount, years, annualRate) {
    const months = years * 12;
    const monthlyRate = annualRate / 12;
    
    // Future value of annuity formula
    const futureValue = monthlyAmount * (((1 + monthlyRate) ** months - 1) / monthlyRate);
    const totalContribution = monthlyAmount * months;
    const capitalGains = futureValue - totalContribution;
    
    return {
        futureValue: Math.round(futureValue),
        totalContribution: Math.round(totalContribution),
        capitalGains: Math.round(capitalGains)
    };
}

// Enhanced Value Animation
function animateValue(elementId, targetValue, formatter) {
    const element = document.getElementById(elementId);
    if (!element) return;

    const startValue = parseFloat(element.textContent.replace(/[^\d.-]/g, '')) || 0;
    const duration = 1200;
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

// Enhanced Chart Update with Smooth Animations
function updateChart(monthlyAmount, years, scenarios) {
    const svg = document.getElementById('wealthChart');
    if (!svg) return;

    // Generate and animate paths for each scenario
    Object.entries(scenarios).forEach(([scenario, rate], index) => {
        const pathId = `projection-${scenario}`;
        const path = document.getElementById(pathId);
        
        if (path) {
            const newPath = generateProjectionPath(monthlyAmount, years, rate);
            
            // Animate path update
            setTimeout(() => {
                path.style.transition = 'd 0.8s cubic-bezier(0.4, 0, 0.2, 1)';
                path.setAttribute('d', newPath);
            }, index * 100);
        }
    });

    // Update time axis
    updateTimeAxis(svg, years);
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

// Update Time Axis Labels
function updateTimeAxis(svg, years) {
    const timeLabels = svg.querySelectorAll('.time-axis text');
    const currentYear = new Date().getFullYear();
    
    timeLabels.forEach((label, index) => {
        const yearOffset = (index / (timeLabels.length - 1)) * years;
        const targetYear = currentYear + Math.round(yearOffset);
        
        // Animate year change
        label.style.transition = 'opacity 0.3s ease';
        label.style.opacity = '0';
        
        setTimeout(() => {
            label.textContent = targetYear.toString();
            label.style.opacity = '1';
        }, 150);
    });
}
</script>

</body>

</html>