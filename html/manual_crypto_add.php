<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../api/market_data.php';

$id = isset($_GET['id']) ? trim((string)$_GET['id']) : '';
if ($id === '') { header('Location: manual_crypto.php'); exit; }
$symbol = isset($_GET['symbol']) ? trim((string)$_GET['symbol']) : '';
$info = coingecko_coin_info($id);
$name = $info['ok'] ? (string)$info['name'] : ucfirst($id);
$image = $info['ok'] ? (string)$info['image'] : '';
// Normalise et ignore les valeurs invalides (undefined/null)
if ($symbol !== '' && in_array(strtolower($symbol), ['undefined','null','none'], true)) { $symbol = ''; }
if ($symbol === '' && !empty($info['symbol'])) { $symbol = (string)$info['symbol']; }
$quoteAgg = crypto_price_aggregator($id, $symbol, 'eur');
// Fallback côté serveur: dernier prix unitaire connu (approx) si l'API est indisponible
$lastUnit = null;
if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT pm.quantity, pm.purchase_price, c.balance_eur
                                FROM positions_manual pm
                                LEFT JOIN compte c
                                  ON c.user_id = pm.user_id AND c.source = 'manual' AND c.external_id = pm.external_id
                                WHERE pm.user_id = ? AND pm.categorie = 'crypto' AND pm.external_id = ?
                                LIMIT 1");
        $stmt->execute([$_SESSION['user_id'], $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $qty = isset($row['quantity']) ? (float)$row['quantity'] : 0.0;
            $bal = isset($row['balance_eur']) ? (float)$row['balance_eur'] : 0.0;
            $pp  = isset($row['purchase_price']) ? (float)$row['purchase_price'] : 0.0;
            if ($qty > 0 && $bal > 0) { $lastUnit = $bal / $qty; }
            elseif ($pp > 0) { $lastUnit = $pp; }
        }
    } catch (Throwable $e) { /* ignore fallback */ }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ajouter de la crypto · Nova</title>
  <link rel="stylesheet" href="../css/app.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <style>
    body { background:#0f1115; color:#e6e6e6; font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial; }
    .wrap { max-width: 960px; margin: 40px auto; padding: 16px; }
    .header { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
    .back { color:#9aa0a6; text-decoration:none; }
    .form { border:1px solid #222; background:#111; border-radius:12px; padding:16px; }
    .row { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    .field { display:flex; flex-direction:column; gap:6px; }
    .label { color:#9aa0a6; font-size:0.9rem; }
    .value { display:flex; align-items:center; gap:10px; }
    .avatar { width:24px; height:24px; border-radius:50%; background:#1b1b1b; overflow:hidden; display:flex; align-items:center; justify-content:center; font-weight:600; }
    .input { background:#0d0f14; border:1px solid #222; border-radius:8px; padding:10px; color:#fff; }
    .actions { display:flex; justify-content:flex-end; margin-top:14px; }
    .btn { background:#f4b169; color:#1b1b1b; border:none; border-radius:20px; padding:10px 16px; cursor:pointer; font-weight:600; }
    .quote { color:#9aa0a6; margin-top:8px; }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="header">
      <a class="back" href="manual_crypto.php">← Retour</a>
      <h1>Ajouter de la crypto</h1>
    </div>
    <form class="form" action="save_manual_asset.php" method="post">
      <input type="hidden" name="category" value="crypto" />
      <input type="hidden" name="type" value="crypto" />
      <input type="hidden" name="external_id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>" />
      <input type="hidden" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" />

      <div class="field">
        <div class="label">Nom</div>
        <div class="value">
          <div class="avatar"><?php if ($image !== ''): ?><img src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:100%;height:100%;object-fit:cover" /><?php endif; ?></div>
          <div><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></div>
        </div>
        <?php if (!empty($quoteAgg['ok']) || $lastUnit !== null): ?>
          <div class="quote">
            Cours courant: <?= htmlspecialchars((string)number_format((!empty($quoteAgg['ok']) ? (float)$quoteAgg['price'] : (float)$lastUnit), 2, ',', ' '), ENT_QUOTES, 'UTF-8') ?> EUR<?= !empty($quoteAgg['ok']) ? '' : ' (approx.)' ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="row" style="margin-top:12px">
        <div class="field">
          <div class="label">Quantité</div>
          <input class="input" type="number" name="quantity" step="0.00000001" min="0" required />
        </div>
        <div class="field">
          <div class="label">Prix d'achat</div>
          <input class="input" type="number" name="purchase_price" step="0.00000001" min="0" />
        </div>
      </div>

      <div class="actions">
        <button class="btn" type="submit">Valider</button>
      </div>
    </form>
  </main>
  <script>
    document.addEventListener('DOMContentLoaded', async () => {
      const id = <?= json_encode($id) ?>;
      const name = <?= json_encode($name) ?>;
      const avatarEl = document.querySelector('.avatar');
      // Fallback avatar: première lettre
      if (avatarEl && avatarEl.childElementCount === 0) {
        const letter = (name || id).charAt(0).toUpperCase();
        avatarEl.textContent = letter;
      }

      // Toujours tenter d'obtenir/mettre à jour le cours courant côté client
      try {
        const r = await fetch(`api_market_quote.php?type=crypto&id=${encodeURIComponent(id)}&symbol=${encodeURIComponent(<?= json_encode(strtoupper($symbol)) ?>)}&currency=EUR`, { cache: 'no-store' });
        const j = await r.json();
        const price = (j && (j.price_eur ?? j.price ?? (j.data && j.data.price))) || null;
        if (price !== null) {
          const formatted = Number(price).toLocaleString('fr-FR', {maximumFractionDigits: 8});
          let quoteEl = document.querySelector('.quote');
          if (quoteEl) {
            quoteEl.textContent = `Cours courant: ${formatted} EUR`;
          } else {
            const el = document.createElement('div');
            el.className = 'quote';
            el.textContent = `Cours courant: ${formatted} EUR`;
            const field = document.querySelector('.field');
            if (field) field.appendChild(el);
          }
        }
      } catch (e) {
        // Optionnel: tenter un appel direct CoinGecko si le serveur est indisponible
        try {
          const r2 = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${encodeURIComponent(id)}&vs_currencies=eur`);
          const j2 = await r2.json();
          const price2 = j2 && j2[id] && j2[id].eur;
          if (price2) {
            const formatted = Number(price2).toLocaleString('fr-FR', {maximumFractionDigits: 8});
            let quoteEl = document.querySelector('.quote');
            if (quoteEl) {
              quoteEl.textContent = `Cours courant: ${formatted} EUR`;
            } else {
              const el = document.createElement('div');
              el.className = 'quote';
              el.textContent = `Cours courant: ${formatted} EUR`;
              const field = document.querySelector('.field');
              if (field) field.appendChild(el);
            }
          }
        } catch (_) { /* ignore */ }
      }
    });
  </script>
</body>
</html>