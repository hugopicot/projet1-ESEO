document.addEventListener('DOMContentLoaded', async function() {
  const id = document.querySelector('[data-crypto-id]')?.getAttribute('data-crypto-id');
  const name = document.querySelector('[data-crypto-name]')?.getAttribute('data-crypto-name');
  const avatarEl = document.querySelector('.avatar');
  if (avatarEl && avatarEl.childElementCount === 0) {
    const letter = (name || id || '?').charAt(0).toUpperCase();
    avatarEl.textContent = letter;
  }

  if (!id) return;

  try {
    const r = await fetch(`api_market_quote.php?type=crypto&id=${encodeURIComponent(id)}&currency=EUR`, { cache: 'no-store' });
    const j = await r.json();
    const price = (j && (j.price_eur ?? j.price ?? (j.data && j.data.price))) || null;
    if (price !== null) {
      const formatted = Number(price).toLocaleString('fr-FR', { maximumFractionDigits: 8 });
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
    try {
      const r2 = await fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${encodeURIComponent(id)}&vs_currencies=eur`);
      const j2 = await r2.json();
      const price2 = j2 && j2[id] && j2[id].eur;
      if (price2) {
        const formatted = Number(price2).toLocaleString('fr-FR', { maximumFractionDigits: 8 });
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
