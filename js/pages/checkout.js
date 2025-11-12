document.addEventListener('DOMContentLoaded', function() {
  const btn = document.getElementById('pay');
  const status = document.getElementById('status');
  if (!btn) return;
  btn.addEventListener('click', async function() {
    btn.disabled = true;
    if (status) status.textContent = 'Création de la session…';
    try {
      const res = await fetch('/html/create_checkout_session.php', { method: 'POST' });
      const text = await res.text();
      let data;
      try { data = JSON.parse(text.replace(/^\uFEFF/, '')); } catch (e) { if (status) status.textContent = 'Erreur réseau: ' + e.message; btn.disabled = false; return; }
      if (data.url) {
        window.location.href = data.url;
      } else {
        if (status) status.textContent = 'Erreur: ' + (data.error || 'inconnue');
        btn.disabled = false;
      }
    } catch (e) {
      if (status) status.textContent = 'Erreur réseau';
      btn.disabled = false;
    }
  });
});