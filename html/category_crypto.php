<?php
// Page intermédiaire: Ajout crypto
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ajouter de la crypto</title>
  <link rel="stylesheet" href="../css/app.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <style>
    body { background:#0f1115; color:#e6e6e6; font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial; }
    .wrap { max-width: 960px; margin: 40px auto; padding: 16px; }
    .header { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
    .grid { display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; }
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
      <h1>Ajouter de la crypto</h1>
    </div>
    <section class="grid">
      <div class="card">
        <h2>Exchange</h2>
        <p>Connexion sécurisée et mise à jour automatique de vos comptes d’échange.</p>
        <a class="btn-link" href="connect_powens.php?connector_capabilities=bankwealth">Continuer</a>
      </div>
      <div class="card">
        <h2>Wallet crypto</h2>
        <p>Synchronisez des wallets tels que Ledger, MetaMask…</p>
        <!-- Intentionnellement sans redirection pour le moment -->
      </div>
      <div class="card">
        <h2>Ajout manuel</h2>
        <p>10 000 cryptos disponibles, du Bitcoin aux altcoins.</p>
        <!-- Intentionnellement sans redirection pour le moment -->
      </div>
    </section>
  </main>
</body>
</html>