<?php
// Démo intégration Moralis (Web3 Data API) pour consulter balances, tokens et NFTs
session_start();
require_once '../config/config.php';

function moralis_get(string $path, array $params = []) {
    if (!defined('MORALIS_API_KEY') || MORALIS_API_KEY === '') {
        throw new RuntimeException('MORALIS_API_KEY manquant. Définissez la variable d’environnement ou mettez la clé dans config.');
    }
    $base = rtrim(MORALIS_API_BASE, '/');
    $url = $base . $path;
    if (!empty($params)) {
        $qs = http_build_query($params);
        $url .= (strpos($url, '?') === false ? '?' : '&') . $qs;
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HTTPHEADER => [
            'accept: application/json',
            'X-API-Key: ' . MORALIS_API_KEY,
        ],
    ]);
    $resp = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($status >= 200 && $status < 300) {
        return json_decode((string)$resp, true);
    }
    throw new RuntimeException('Moralis API error (' . $status . '): ' . ($resp ?: $err));
}

$address = isset($_GET['address']) ? trim((string)$_GET['address']) : '';
$chain   = isset($_GET['chain']) ? trim((string)$_GET['chain']) : 'eth';
$error   = '';
$native  = null; // { balance }
$tokens  = null; // { result: [...] }
$nfts    = null; // { result: [...] }

if ($address !== '') {
    if (!defined('MORALIS_API_KEY') || MORALIS_API_KEY === '') {
        // Mode statique pour débutants: exemples sans requêtes HTTP
        $native = ['balance' => '1000000000000000000']; // 1 ETH
        $tokens = ['result' => [
            ['token_address' => '0xtokenAAA', 'symbol' => 'AAA', 'name' => 'Token AAA', 'balance' => '123450000000000000', 'price' => ['usd' => 1.23]],
            ['token_address' => '0xtokenBBB', 'symbol' => 'BBB', 'name' => 'Token BBB', 'balance' => '987650000000000000', 'price' => ['usd' => 0.45]],
        ]];
        $nfts = ['result' => []];
        $error = '';
    } else {
        try {
            // Native balance (ETH/MATIC/etc.)
            $native = moralis_get('/' . $address . '/balance', ['chain' => $chain]);
            // Token balances avec prix
            $tokens = moralis_get('/wallets/' . $address . '/tokens', ['chain' => $chain]);
            // NFTs par wallet
            $nfts   = moralis_get('/' . $address . '/nft', ['chain' => $chain]);
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Moralis · Démo Web3</title>
  <link rel="stylesheet" href="../css/app.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <style>
    body { background:#0f1115; color:#e6e6e6; font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; }
    .wrap { max-width: 980px; margin: 40px auto; padding: 24px; }
    .card { background:#111; border:1px solid #222; border-radius:12px; padding:16px; margin-bottom:16px; }
    .row { display:grid; grid-template-columns: 1fr 1fr; gap:12px; }
    .item { background:#0c0c0c; border:1px solid #222; border-radius:10px; padding:12px; }
    label { display:block; margin-bottom:6px; color:#bbb; }
    input, select { width:100%; padding:8px 10px; border-radius:8px; border:1px solid #333; background:#0b0b0b; color:#e6e6e6; }
    button { padding:10px 14px; border-radius:8px; border:1px solid #333; background:#1b1b1b; color:#fff; cursor:pointer; }
    h1 { font-size:1.6rem; margin:0 0 12px; }
    h2 { font-size:1.2rem; margin:0 0 8px; }
    code { background:#151515; padding:2px 4px; border-radius:6px; }
    .hint { color:#a9a9a9; font-size:0.95rem; }
    .error { color:#ff6b6b; }
    table { width:100%; border-collapse: collapse; }
    th, td { text-align:left; padding:8px; border-bottom:1px solid #222; }
  </style>
  <meta name="robots" content="noindex" />
  <meta name="referrer" content="no-referrer" />
  <link rel="icon" type="image/png" href="../asset/img/logo.png" />
  <link rel="apple-touch-icon" href="../asset/img/logo.png" />
  <meta name="color-scheme" content="dark light" />
  <meta http-equiv="Cache-Control" content="no-store" />
  <meta http-equiv="Pragma" content="no-cache" />
  <meta http-equiv="Expires" content="0" />
</head>
<body>
  <main class="wrap">
    <div class="card">
      <h1>Moralis · Démo Web3 Data API</h1>
      <p class="hint">Saisissez une adresse EVM (ex. <code>0xd8dA6BF26964aF9D7eEd9e03E53415D37aA96045</code>) et choisissez une chaîne pour voir les données du portefeuille.</p>
      <?php if (MORALIS_API_KEY === ''): ?>
        <p class="error">MORALIS_API_KEY n’est pas configurée. Définissez la variable d’environnement ou mettez la clé dans <code>config/config.php</code>.</p>
      <?php endif; ?>
      <form method="get" action="moralis_demo.php" style="margin-top:12px;">
        <label>Adresse EVM</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($address, ENT_QUOTES, 'UTF-8'); ?>" placeholder="0x..." />
        <div style="margin-top:8px; display:flex; gap:8px; align-items:center;">
          <label>Chaîne</label>
          <select name="chain">
            <?php
            $chains = ['eth','polygon','bsc','avalanche','fantom','arbitrum','base','optimism'];
            foreach ($chains as $c) {
                $sel = ($chain === $c) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($c, ENT_QUOTES, 'UTF-8') . '" ' . $sel . '>' . strtoupper($c) . '</option>';
            }
            ?>
          </select>
          <button type="submit">Afficher</button>
        </div>
      </form>
    </div>

    <?php if ($error !== ''): ?>
      <div class="card"><p class="error">Erreur: <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p></div>
    <?php endif; ?>

    <?php if ($address !== '' && $error === ''): ?>
      <div class="row">
        <div class="item">
          <h2>Solde natif</h2>
          <?php if (is_array($native) && isset($native['balance'])): ?>
            <p><strong>balance (wei):</strong> <code><?php echo htmlspecialchars((string)$native['balance'], ENT_QUOTES, 'UTF-8'); ?></code></p>
          <?php else: ?>
            <p class="hint">Aucun solde lu.</p>
          <?php endif; ?>
        </div>
        <div class="item">
          <h2>Tokens (avec prix)</h2>
          <?php $toks = is_array($tokens) ? ($tokens['result'] ?? []) : []; ?>
          <?php if (!empty($toks)): ?>
            <table>
              <thead><tr><th>Symbole</th><th>Nom</th><th>Balance</th><th>USD</th></tr></thead>
              <tbody>
              <?php foreach ($toks as $tok): ?>
                <tr>
                  <td><?php echo htmlspecialchars($tok['symbol'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($tok['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars($tok['balance_formatted'] ?? ($tok['balance'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                  <td><?php echo htmlspecialchars((string)($tok['usd_value'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="hint">Aucun token détecté.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card">
        <h2>NFTs</h2>
        <?php $n = is_array($nfts) ? ($nfts['result'] ?? []) : []; ?>
        <?php if (!empty($n)): ?>
          <table>
            <thead><tr><th>Contrat</th><th>Token ID</th><th>Nom</th></tr></thead>
            <tbody>
            <?php foreach ($n as $it): ?>
              <tr>
                <td><?php echo htmlspecialchars($it['token_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($it['token_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                <td><?php echo htmlspecialchars($it['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="hint">Aucun NFT détecté.</p>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </main>
</body>
</html>