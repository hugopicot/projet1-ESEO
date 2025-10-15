<?php
session_start();

// Si l'utilisateur a confirmé la déconnexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    header("Location: auth.php"); // Redirige vers la page de connexion
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Déconnexion - NOVA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="logout-page d-flex align-items-center justify-content-center">

<div class="logout-card text-center p-4">
    <h2 class="text-white mb-3">Vous êtes sur le point de vous déconnecter</h2>
    <p class="text-secondary mb-4">Êtes-vous sûr de vouloir quitter votre session ?</p>

    <form action="logout.php" method="POST">
        <button type="submit" class="btn btn-logout me-2">Oui, me déconnecter</button>
        <a href="app.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

</body>
</html>
