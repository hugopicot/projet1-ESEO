<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/adminpanel.css">
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar p-3">
        <h5 class="fw-bold text-white mb-4"><img src="../asset/img/logo.png" alt="Nova" class="logo"></h5>
        <ul class="nav flex-column">
            <li class="nav-item mb-2"><a href="#" class="nav-link active">Dashboard</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link">Marketplace</a></li>
            <li class="nav-item mb-2"><a href="../index.php" class="nav-link">Accueil</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link">Clients</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link">Remises</a></li>
            <hr class="border-secondary">
            <li class="nav-item mb-2"><a href="#" class="nav-link">Comptabilité</a></li>
            <li class="nav-item mb-2"><a href="#" class="nav-link">Taxes</a></li>
            <hr class="border-secondary">
            <li class="nav-item mb-2"><a href="#" class="nav-link">Paramètres</a></li>
        </ul>
        <div class="mt-auto small text-secondary pt-4">
            Siloé BOISSON<br><span class="text-muted">Admin Panel</span>
        </div>
    </div>

    <!-- Main content -->
    <div class="main p-4 flex-fill">
        <h4 class="fw-bold text-white mb-4">Dashboard</h4>

        <!-- Stats Cards -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="fw-semibold text-muted">Total clients</div>
                    <h5 class="fw-bold text-white mt-2">567,899 <span class="text-success fs-6">▲ 2.5%</span></h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="fw-semibold text-muted">Revenu total</div>
                    <h5 class="fw-bold text-white mt-2">$3,465 M <span class="text-success fs-6">▲ 0.5%</span></h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="fw-semibold text-muted">Commandes</div>
                    <h5 class="fw-bold text-white mt-2">1,136 M <span class="text-danger fs-6">▼ 0.2%</span></h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 stat-card">
                    <div class="fw-semibold text-muted">Retours</div>
                    <h5 class="fw-bold text-white mt-2">1,789 <span class="text-success fs-6">▲ 0.12%</span></h5>
                </div>
            </div>
        </div>

        <!-- Graph placeholders -->
        <div class="card p-3 mb-4 stat-card">
            <div class="fw-semibold text-white mb-3">Ventes produits</div>
            <div class="chart-placeholder">[Graphique ventes]</div>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="card p-3 stat-card">
                    <div class="fw-semibold text-white mb-3">Ventes par catégorie</div>
                    <div class="chart-placeholder">[Graphique catégories]</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card p-3 stat-card">
                    <div class="fw-semibold text-white mb-3">Ventes par pays</div>
                    <div class="chart-placeholder">[Graphique pays]</div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>