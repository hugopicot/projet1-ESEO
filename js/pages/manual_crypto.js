document.addEventListener('DOMContentLoaded', function() {
  const resultsList = document.getElementById('results-list');
  const searchEl = document.getElementById('search');
  const clearBtn = document.getElementById('clear');

  function renderList(root, items) {
    root.innerHTML = '';
    items.forEach(function(it) {
      const div = document.createElement('div');
      div.className = 'item';
      const avatar = it.image ? `<img src='${it.image}' alt='' style='width:100%;height:100%;border-radius:50%;object-fit:cover'>` : '₿';
      div.innerHTML = `<div class="avatar">${avatar}</div>
                       <div class="meta"><div class="name">${it.name}</div><div class="sub">${it.sub}</div></div>
                       <div class="tag">${it.tag}</div>`;
      div.addEventListener('click', function() {
        window.location.href = `manual_crypto_add.php?id=${encodeURIComponent(it.id)}&symbol=${encodeURIComponent(it.sub)}`;
      });
      root.appendChild(div);
    });
    root.hidden = items.length === 0;
  }

  let timer;
  searchEl.addEventListener('input', function() {
    clearTimeout(timer);
    const q = searchEl.value.trim();
    clearBtn.hidden = q.length === 0;
    if (q.length < 2) { resultsList.hidden = true; return; }
    timer = setTimeout(async function() {
      try {
        const r = await fetch(`api_market_search.php?type=crypto&q=${encodeURIComponent(q)}`);
        const j = await r.json();
        const items = (j.items || []).map(x => ({ id: x.id, name: x.name, tag: 'CRYPTO', sub: x.symbol.toUpperCase(), image: x.image || '' }));
        renderList(resultsList, items);
      } catch (e) { resultsList.hidden = true; }
    }, 250);
  });

  clearBtn.addEventListener('click', function() {
    searchEl.value = '';
    resultsList.innerHTML = '';
    resultsList.hidden = true;
    clearBtn.hidden = true;
    searchEl.focus();
  });
});
