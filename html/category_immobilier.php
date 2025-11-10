<?php
// Page intermédiaire: Ajout immobilier
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ajouter mon immobilier</title>
  <link rel="stylesheet" href="../css/app.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <style>
    body { background:#0f1115; color:#e6e6e6; font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial; }
    .wrap { max-width: 960px; margin: 40px auto; padding: 16px; }
    .header { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
    .grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px; }
    .card { background:#111; border:1px solid #222; border-radius:12px; padding:20px; min-height:160px; }
    .card h2 { margin:0 0 8px; font-size:1.1rem; }
    .card p { margin:0; color:#b7b7b7; }
    .btn-link { display:inline-flex; align-items:center; gap:8px; margin-top:14px; padding:10px 14px; border-radius:8px; border:1px solid #333; background:#1b1b1b; color:#fff; text-decoration:none; }
    .back { color:#9aa0a6; text-decoration:none; }
  </style>
  </head>
<body>
  <main class="wrap">
    <div class="header">
      <a class="back" href="add_assets.php">← Retour</a>
      <h1>Ajouter mon immobilier</h1>
    </div>
    <section class="grid">
      <div class="card">
        <h2>Immobilier physique</h2>
        <p>Résidence principale, secondaire, immobilier locatif, locaux commerciaux, parking.</p>
        <a class="btn-link" href="connect_powens.php?connector_capabilities=bankwealth&account_types=real_estate&return_to=/html/category_immobilier.php">Continuer</a>
      </div>
      <div class="card">
        <h2>SCPI</h2>
        <p>Choix parmi toutes les SCPI françaises avec informations sur leurs rendements et frais.</p>
        <!-- Intentionnellement sans redirection pour le moment -->
      </div>
    </section>
  </main>
</body>
</html>