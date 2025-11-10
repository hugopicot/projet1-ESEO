<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <title>Paiement test Stripe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body{font-family:Segoe UI,system-ui,Arial,sans-serif;background:#0f1115;color:#e6e6e6;display:flex;align-items:center;justify-content:center;height:100vh;margin:0}
    .card{background:#161a22;border:1px solid #2a2f3a;border-radius:12px;box-shadow:0 8px 20px rgba(0,0,0,.3);padding:24px;max-width:420px;width:100%}
    h1{font-size:20px;margin:0 0 12px}
    p{opacity:.8;margin:0 0 16px}
    button{background:#7b5c2f;color:#fff;border:none;border-radius:8px;padding:12px 16px;font-weight:600;cursor:pointer}
    button:hover{background:#9a6f37}
    .status{margin-top:12px;opacity:.8;font-size:14px}
  </style>
</head>
<body>
  <div class="card">
    <h1>Tester Stripe Checkout</h1>
    <p>Montant: 9,90 € (mode test). Utilisez les cartes de test Stripe (ex: 4242 4242 4242 4242).</p>
    <button id="pay">Payer 9,90 €</button>
    <div class="status" id="status"></div>
  </div>

  <script>
    const btn = document.getElementById('pay');
    const status = document.getElementById('status');
    btn.addEventListener('click', async () => {
      btn.disabled = true; status.textContent = 'Création de la session…';
      try {
        const res = await fetch('/html/create_checkout_session.php', { method: 'POST' });
        const data = await res.json();
        if (data.url) { window.location.href = data.url; }
        else { status.textContent = 'Erreur: ' + (data.error || 'inconnue'); btn.disabled = false; }
      } catch (e) { status.textContent = 'Erreur réseau'; btn.disabled = false; }
    });
  </script>
</body>
</html>