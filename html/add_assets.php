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
    <title>Nova · Ajouter des actifs</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="../css/add-assets.css">
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
                    <a class="sidebar-item" href="analyse.php">
                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/ticket.svg" alt="" loading="lazy">
                        <span class="sidebar-label">Analyse</span>
                    </a>
                </div>
            </div>
            <div class="sidebar-footer only-desktop">
                <div class="dropdown full-width" data-dropdown>
                    <button class="sidebar-item sidebar-button" type="button" aria-haspopup="menu" aria-expanded="false" data-dropdown-toggle="profile-menu">
                        <span class="avatar avatar-square avatar-large" aria-hidden="true">ER</span>
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
                        <a class="dropdown-item" href="#" role="menuitem">
                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/cog-6-tooth.svg" alt="" loading="lazy">
                            <span class="dropdown-label">Paramètres</span>
                        </a>
                        <a class="dropdown-item" href="../index.php" role="menuitem">
                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/arrow-right-start-on-rectangle.svg" alt="" loading="lazy">
                            <span class="dropdown-label">Se déconnecter</span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <main class="app-content" id="main-content" tabindex="-1">
            <section class="content-section add-assets-page" aria-label="Ajout d'actifs">
                <h1>Compléter mon portefeuille</h1>

                <div class="category-toolbar" role="search">
                    <div class="search-control">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M10.5 3a7.5 7.5 0 015.906 12.179l3.207 3.207a1 1 0 11-1.414 1.414l-3.207-3.207A7.5 7.5 0 1110.5 3zm0 2a5.5 5.5 0 100 11 5.5 5.5 0 000-11z" />
                        </svg>
                        <input id="category-search" class="search-input" type="search" placeholder="Recherchez: Révolut, Bourse, Bitcoin…" aria-label="Rechercher une catégorie">
                    </div>
                </div>

                <div class="category-grid" role="group" aria-label="Toutes les catégories" id="category-grid">
                    <button type="button" class="category-card" aria-label="Comptes bancaires" data-action="connect-powens">
                        <div class="category-content">
                            <h3 class="category-title">Comptes bancaires</h3>
                            <p class="category-desc">Saisissez vos comptes courants et d’épargne.</p>
                        </div>
                        <div class="category-art" aria-hidden="true"></div>
                    </button>

                    <button type="button" class="category-card" aria-label="Actions & Fonds" data-action="connect-powens-invest">
                        <div class="category-content">
                            <h3 class="category-title">Actions & Fonds</h3>
                            <p class="category-desc">Ajoutez vos positions boursières et ETF.</p>
                        </div>
                        <div class="category-art" aria-hidden="true"></div>
                    </button>

                    <button type="button" class="category-card" aria-label="Crypto" data-action="connect-crypto">
                        <div class="category-content">
                            <h3 class="category-title">Crypto</h3>
                            <p class="category-desc">Indiquez vos montants en wallets ou plateformes.</p>
                        </div>
                        <div class="category-art" aria-hidden="true"></div>
                    </button>

                    <button type="button" class="category-card" aria-label="Immobilier" data-action="connect-powens-real-estate">
                        <div class="category-content">
                            <h3 class="category-title">Immobilier</h3>
                            <p class="category-desc">Ajoutez biens, parts et estimations.</p>
                        </div>
                        <div class="category-art" aria-hidden="true"></div>
                    </button>

                    <button type="button" class="category-card" aria-label="Assurance vie" data-action="connect-powens-lifeinsurance">
                        <div class="category-content">
                            <h3 class="category-title">Assurance vie</h3>
                            <p class="category-desc">Saisissez fonds euros et unités de compte.</p>
                        </div>
                        <div class="category-art" aria-hidden="true"></div>
                    </button>

                    <button type="button" class="category-card" aria-label="Autre">
                        <div class="category-content">
                            <h3 class="category-title">Autre</h3>
                            <p class="category-desc">Actifs hors catégories (or, collection, etc.).</p>
                        </div>
                        <div class="category-art" aria-hidden="true"></div>
                    </button>
                </div>
                <div class="category-empty" id="category-empty" hidden>Aucune catégorie ne correspond à votre recherche.</div>
            </section>
        </main>
    </div>
</div>

<script>
// Animation d’entrée douce et gestion basique des dropdowns profil
document.addEventListener('DOMContentLoaded', function() {
    const page = document.querySelector('.add-assets-page');
    if (page) {
        page.classList.add('is-entered');
    }

    const toggles = document.querySelectorAll('[data-dropdown-toggle]');
    const closeAll = () => {
        document.querySelectorAll('.dropdown-menu').forEach(m => m.style.display = 'none');
    };
    toggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-dropdown-toggle');
            const menu = document.getElementById(id);
            if (!menu) return;
            const isOpen = menu.style.display === 'block';
            closeAll();
            menu.style.display = isOpen ? 'none' : 'block';
        });
    });
    document.addEventListener('click', function(e) {
        if (!e.target.closest('[data-dropdown]')) {
            closeAll();
        }
    });

    // Filtrage des catégories (front-only)
    const searchInput = document.getElementById('category-search');
    const cards = Array.from(document.querySelectorAll('.category-card'));
    const emptyState = document.getElementById('category-empty');
    const filter = (q) => {
        const query = (q || '').toLowerCase().trim();
        let matches = 0;
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            const show = query === '' || text.includes(query);
            card.style.display = show ? '' : 'none';
            if (show) matches++;
        });
        if (emptyState) {
            emptyState.hidden = matches !== 0;
        }
    };
    if (searchInput) {
        searchInput.addEventListener('input', (e) => filter(e.target.value));
    }

    // Action: page intermédiaire "Comptes bancaires"
    const bankButton = document.querySelector('.category-card[data-action="connect-powens"]');
    if (bankButton) {
        bankButton.addEventListener('click', function() {
            window.location.href = 'category_bank.php';
        });
    }

    // Action: page intermédiaire "Crypto"
    const cryptoButton = document.querySelector('.category-card[data-action="connect-crypto"]');
    if (cryptoButton) {
        cryptoButton.addEventListener('click', function() {
            window.location.href = 'category_crypto.php';
        });
    }

    // Actions & Fonds: page intermédiaire
    const investButton = document.querySelector('.category-card[data-action="connect-powens-invest"]');
    if (investButton) {
        investButton.addEventListener('click', function() {
            window.location.href = 'category_actions.php';
        });
    }

    // Assurance vie: page intermédiaire
    const lifeButton = document.querySelector('.category-card[data-action="connect-powens-lifeinsurance"]');
    if (lifeButton) {
        lifeButton.addEventListener('click', function() {
            window.location.href = 'category_assurance.php';
        });
    }

    // Immobilier: page intermédiaire
    const reButton = document.querySelector('.category-card[data-action="connect-powens-real-estate"]');
    if (reButton) {
        reButton.addEventListener('click', function() {
            window.location.href = 'category_immobilier.php';
        });
    }
});
</script>

</body>
</html>