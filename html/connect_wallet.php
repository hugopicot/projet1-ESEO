<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ./auth.php');
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Connexion Wallet Crypto</title>
  <link rel="stylesheet" href="../css/app.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <style>
    body { background:#0f1115; color:#e6e6e6; font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial; }
    .wrap { max-width: 720px; margin: 40px auto; padding: 24px; }
    .card { background:#111; border:1px solid #222; border-radius:12px; padding:20px; }
    .btn { padding:10px 14px; border-radius:8px; border:1px solid #333; background:#1b1b1b; color:#fff; cursor:pointer; }
    .row { display:flex; gap:12px; align-items:center; }
    input { width:100%; padding:10px; border-radius:8px; border:1px solid #333; background:#0b0b0b; color:#e6e6e6; }
    .hint { color:#aaa; }
    .error { color:#ff6b6b; }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="card">
      <h1>Connecter votre wallet crypto</h1>
      <p class="hint">Vous pouvez connecter MetaMask pour lier votre adresse à votre compte Nova et analyser votre portefeuille via Moralis.</p>
      <div style="margin-top:12px;">
        <button id="btn-metamask" class="btn">Connecter MetaMask</button>
      </div>
      <hr style="border-color:#222; margin:16px 0;" />
      <p class="hint">Si vous n’avez pas MetaMask, entrez votre adresse EVM ci-dessous :</p>
      <div class="row">
        <input id="manual-address" type="text" placeholder="0x…" />
        <button id="btn-continue" class="btn">Continuer</button>
      </div>
      <p id="status" class="hint" style="margin-top:12px;"></p>
    </div>
  </main>

  <script>
    function redirectSave(addr) {
      if (!addr) return;
      // Redirige vers l’endpoint qui enregistre l’adresse pour l’utilisateur
      window.location.href = 'save_wallet.php?address=' + encodeURIComponent(addr);
    }

    document.getElementById('btn-metamask')?.addEventListener('click', async function() {
      const status = document.getElementById('status');
      try {
        if (!window.ethereum || !window.ethereum.request) {
          status.textContent = 'MetaMask non détecté. Utilisez le champ adresse ci-dessous.';
          return;
        }
        status.textContent = 'Demande de connexion MetaMask…';
        const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
        const addr = (accounts && accounts[0]) ? accounts[0] : '';
        if (!addr) { status.textContent = 'Aucune adresse retournée par MetaMask.'; return; }
        redirectSave(addr);
      } catch (e) {
        status.textContent = 'Erreur MetaMask: ' + (e && e.message ? e.message : 'inconnue');
      }
    });

    document.getElementById('btn-continue')?.addEventListener('click', function() {
      const input = document.getElementById('manual-address');
      const addr = (input && input.value || '').trim();
      if (!addr) {
        document.getElementById('status').textContent = 'Saisissez une adresse EVM (0x…).';
        return;
      }
      redirectSave(addr);
    });
  </script>
</body>
</html>