<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once __DIR__ . '/../config/config.php';
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Ajouter crypto · Nova</title>
  <link rel="stylesheet" href="../css/app.css" />
  <link rel="stylesheet" href="../css/styles.css" />
  <style>
    body { background:#0f1115; color:#e6e6e6; font-family: system-ui,-apple-system,Segoe UI,Roboto,Arial; }
    .wrap { max-width: 960px; margin: 40px auto; padding: 16px; }
    .header { display:flex; align-items:center; gap:12px; margin-bottom:20px; }
    .back { color:#9aa0a6; text-decoration:none; }
    .search { display:flex; align-items:center; gap:10px; background:#12151c; border:1px solid #222; border-radius:12px; padding: 10px 12px; }
    .search input { flex:1; background:transparent; border:none; color:#e6e6e6; outline:none; }
    .search input::-webkit-search-cancel-button { -webkit-appearance: none; }
    .clear-btn { background: transparent; border: none; color: #fff; font-size: 18px; line-height: 1; cursor: pointer; padding: 0 4px; }
    .section-title { margin:16px 0; color:#9aa0a6; font-size:0.95rem; }
    .list { display:flex; flex-direction:column; gap:8px; }
    .item { display:flex; align-items:center; gap:12px; padding:10px 12px; border-radius:10px; border:1px solid #222; background:#111; cursor:pointer; }
    .item:hover { background:#151515; }
    .avatar { width:28px; height:28px; border-radius:50%; background:#1b1b1b; display:flex; align-items:center; justify-content:center; font-size:0.8rem; }
    .meta { display:flex; flex-direction:column; }
    .meta .name { font-weight:600; }
    .meta .sub { color:#9aa0a6; font-size:0.9rem; }
    .tag { margin-left:auto; font-size:0.75rem; padding:4px 8px; border-radius:8px; border:1px solid #333; background:#1b1b1b; color:#cbd5e1; }
    .quote { color:#9aa0a6; margin-top:8px; }
  </style>
</head>
<body>
  <main class="wrap">
    <div class="header">
      <a class="back" href="patrimoine.php">← Retour</a>
      <h1>Ajouter crypto</h1>
    </div>

    <div class="search" role="search">
      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M10.5 3a7.5 7.5 0 015.906 12.179l3.207 3.207a1 1 0 11-1.414 1.414l-3.207-3.207A7.5 7.5 0 1110.5 3zm0 2a5.5 5.5 0 100 11 5.5 5.5 0 000-11z"/></svg>
      <input id="search" type="search" placeholder="Chercher une crypto" autocomplete="off" aria-label="Recherche crypto" />
      <button id="clear" class="clear-btn" type="button" aria-label="Effacer" hidden>×</button>
    </div>

    <div class="list" id="results-list" aria-label="Résultats" hidden></div>
  </main>

  <script type="text/plain" data-disabled="true">
    const resultsList = document.getElementById('results-list');
    const searchEl = document.getElementById('search');
    const clearBtn = document.getElementById('clear');

    function renderList(root, items){
      root.innerHTML = '';
      items.forEach(it => {
        const div = document.createElement('div');
        div.className = 'item';
        const avatar = it.image ? `<img src='${it.image}' alt='' style='width:100%;height:100%;border-radius:50%;object-fit:cover'>` : '₿';
        div.innerHTML = `<div class=\"avatar\">${avatar}</div>
                         <div class=\"meta\"><div class=\"name\">${it.name}</div><div class=\"sub\">${it.sub}</div></div>
                         <div class=\"tag\">${it.tag}</div>`;
        div.addEventListener('click', () => { window.location.href = `manual_crypto_add.php?id=${encodeURIComponent(it.id)}&symbol=${encodeURIComponent(it.sub)}`; });
        root.appendChild(div);
      });
      root.hidden = items.length === 0;
    }

    async function selectInstrument(id, displayName){
      window.location.href = `manual_crypto_add.php?id=${encodeURIComponent(id)}`;
    }

    let timer;
    searchEl.addEventListener('input', () => {
      clearTimeout(timer);
      const q = searchEl.value.trim();
      clearBtn.hidden = q.length === 0;
      if (q.length < 2){
        resultsList.hidden = true;
        return;
      }
      timer = setTimeout(async () => {
        try {
          const r = await fetch(`api_market_search.php?type=crypto&q=${encodeURIComponent(q)}`);
          const j = await r.json();
          const items = (j.items||[]).map(x => ({id:x.id, name:x.name, tag:'CRYPTO', sub:x.symbol.toUpperCase(), image:x.image||''}));
          renderList(resultsList, items);
        } catch(e){ resultsList.hidden = true; }
      }, 250);
    });

    clearBtn.addEventListener('click', () => {
      searchEl.value = '';
      resultsList.innerHTML = '';
      resultsList.hidden = true;
      clearBtn.hidden = true;
      searchEl.focus();
    });
  </script>
  <script src="../js/pages/manual_crypto.js"></script>
</body>
</html>

