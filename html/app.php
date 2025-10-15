<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ./auth.php");
    exit;
}
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
            <div class="sidebar-header">
                <div class="dropdown full-width" data-dropdown>
                    <button class="sidebar-item sidebar-button" type="button" aria-haspopup="menu" aria-expanded="false" data-dropdown-toggle="team-menu">
                        <span class="avatar" aria-hidden="true">TL</span>
                        <span class="sidebar-label">Tailwind Labs</span>
                        <img class="icon icon-chevron" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/chevron-down.svg" alt="" loading="lazy">
                        <img class="icon icon-chevron icon-chevron-alt" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/chevron-up.svg" alt="" loading="lazy">
                        <span class="sr-only">Changer d'équipe</span>
                    </button>
                    <div class="dropdown-menu bottom-start" id="team-menu" role="menu">
                        <a class="dropdown-item" href="#team-settings" role="menuitem">
                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/cog-8-tooth.svg" alt="" loading="lazy">
                            <span class="dropdown-label">Paramètres</span>
                        </a>
                        <div class="dropdown-divider" role="none"></div>
                        <a class="dropdown-item" href="#tailwind" role="menuitem">
                            <span class="dropdown-icon avatar-pill" aria-hidden="true">TL</span>
                            <span class="dropdown-label">Tailwind Labs</span>
                        </a>
                        <a class="dropdown-item" href="#workcation" role="menuitem">
                            <span class="dropdown-icon avatar-pill avatar-purple" aria-hidden="true">WC</span>
                            <span class="dropdown-label">Workcation</span>
                        </a>
                        <div class="dropdown-divider" role="none"></div>
                        <a class="dropdown-item" href="#nouvelle-equipe" role="menuitem">
                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/plus.svg" alt="" loading="lazy">
                            <span class="dropdown-label">Nouvelle équipe…</span>
                        </a>
                    </div>
                </div>
                <div class="sidebar-section only-desktop">
                    <a class="sidebar-item" href="#search">
                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/magnifying-glass.svg" alt="" loading="lazy">
                        <span class="sidebar-label">Recherche</span>
                    </a>
                    <a class="sidebar-item" href="#inbox">
                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/inbox.svg" alt="" loading="lazy">
                        <span class="sidebar-label">Messagerie</span>
                    </a>
                </div>
            </div>

            <div class="sidebar-body">
                <div class="sidebar-section">
                    <a class="sidebar-item is-active" href="../index.php">
                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/home.svg" alt="" loading="lazy">
                        <span class="sidebar-label">Accueil</span>
                    </a>
                    <a class="sidebar-item" href="#evenements">
                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/square-2-stack.svg" alt="" loading="lazy">
                        <span class="sidebar-label">Événements</span>
                    </a>
                    <a class="sidebar-item" href="#commandes">
                        <img class="icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/ticket.svg" alt="" loading="lazy">
                        <span class="sidebar-label">Commandes</span>
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

                <div class="sidebar-section only-desktop">
                    <p class="sidebar-heading">Événements à venir</p>
                    <a class="sidebar-link" href="#event-1">Bear Hug — Live in Concert</a>
                    <a class="sidebar-link" href="#event-2">Viking People</a>
                    <a class="sidebar-link" href="#event-3">Six Fingers — DJ Set</a>
                    <a class="sidebar-link" href="#event-4">We All Look The Same</a>
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
                            <span class="sidebar-user-name"><?= htmlspecialchars($_SESSION['username']) ?></span>
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
                        <a class="dropdown-item" href="./logout.php" role="menuitem">
                            <img class="dropdown-icon" src="https://raw.githubusercontent.com/tailwindlabs/heroicons/master/src/24/solid/arrow-right-start-on-rectangle.svg" alt="" loading="lazy">
                            <span class="dropdown-label">Se déconnecter </span>
                        </a>
                    </div>
                </div>
            </div>
        </aside>

        <main class="app-content" id="main-content" tabindex="-1">
            <section class="content-section">
                <h1>Bienvenue sur la console Nova</h1>
                <p>
                    Ce squelette reproduit la structure Sidebar + Navbar fournie. Remplacez cette zone par vos contenus applicatifs.
                </p>
                <div class="content-grid">
                    <article class="content-card">
                        <h2>Étape suivante</h2>
                        <p>Personnalisez les éléments du menu et connectez-les à vos routes ou actions.</p>
                    </article>
                    <article class="content-card">
                        <h2>Actions rapides</h2>
                        <p>Utilisez les menus déroulants pour proposer des paramètres, de l’aide ou des raccourcis utiles.</p>
                    </article>
                    <article class="content-card">
                        <h2>État système</h2>
                        <p>Affichez ici des indicateurs clés ou un tableau de bord synthétique adapté à vos besoins.</p>
                    </article>
                </div>
            </section>
        </main>
    </div>
</div>

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
});

</script>
</body>
</html>
