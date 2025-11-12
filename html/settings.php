<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Paramètres - Nova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/app.css">
    <link rel="stylesheet" href="../css/patrimoine.css">
</head>

<body class="settings-page">
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
  <a class="icon-button" href="http://hugo:8090/html/manual_crypto.php" aria-label="Ajouter des actifs" title="Ajouter des actifs">
            <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 5a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H6a1 1 0 110-2h5V6a1 1 0 011-1z" />
            </svg>
            <span class="sr-only">Ajouter des actifs</span>
        </a>
    </nav>
 </header>
<div class="container-fluid">
    <div class="row flex-nowrap">

        <!-- SIDEBAR -->
        <div class="col-auto col-md-3 col-xl-2 px-sm-2 px-0 sidebar">
            <div class="d-flex flex-column align-items-center align-items-sm-start px-3 pt-3 text-white min-vh-100">
                <a href="#" class="d-flex align-items-center mb-3 text-white text-decoration-none">
                    <span class="fs-4 fw-bold text-lime">⚙️ Settings</span>
                </a>

                <ul class="nav nav-pills flex-column mb-auto w-100" id="menu">
                    <li class="nav-item">
                        <a href="./profil.php" class="nav-link text-white">
                            <i class="bi bi-person-circle me-2"></i> Profil
                        </a>
                    </li>
                    <li>
                        <a href="./settings.php" class="nav-link text-white active">
                            <i class="bi bi-gear me-2"></i> Paramètres
                        </a>
                    </li>
                </ul>


                <hr class="text-secondary w-100">
                <div class="mt-auto mb-3">
                    <a href="app.php" class="btn btn-cancel w-100">⬅ Retour</a>
                </div>
            </div>
        </div>

        <!-- CONTENU PRINCIPAL -->
        <div class="col py-5 px-4 main-content">
            <div class="tab-content">
                <div class="col py-5 px-4 main-content">

                    <div class="card-dark p-4 rounded-4">
                        <h3 class="text-lime mb-4">⚙️ Paramètres du compte</h3>

                        <form>
                            <div class="mb-3">
                                <label class="form-label text-secondary">Langue de l’interface</label>
                                <select class="form-select input-dark">
                                    <option>Français</option>
                                    <option>Anglais</option>
                                    <option>Espagnol</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary">Thème</label>
                                <select class="form-select input-dark">
                                    <option>Sombre (par défaut)</option>
                                    <option>Clair</option>
                                </select>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="notifications" checked>
                                <label class="form-check-label text-secondary" for="notifications">Recevoir les notifications</label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="2fa">
                                <label class="form-check-label text-secondary" for="2fa">Activer la double authentification (2FA)</label>
                            </div>

                            <button type="submit" class="btn btn-lime mt-2">✅ Sauvegarder</button>
                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
