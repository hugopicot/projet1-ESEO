document.addEventListener('DOMContentLoaded', function() {
  async function startCheckout(plan) {
    try {
      const res = await fetch('/html/create_subscription_session.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ plan })
      });
      const text = await res.text();
      let data;
      try { data = JSON.parse(text.replace(/^\uFEFF/, '')); } catch (e) { alert('Erreur réseau: ' + e.message); return; }
      if (!res.ok || !data.url) {
        const base = 'Erreur Stripe: ' + (data.error || 'Réponse invalide');
        const details = data && data.details ? ('\nDétail: ' + data.details + (data.status ? ` (HTTP ${data.status})` : '')) : '';
        alert(base + details);
        return;
      }
      window.location.href = data.url;
    } catch (e) {
      alert('Erreur réseau: ' + e.message);
    }
  }

  const starterBtn = document.getElementById('checkout-starter');
  const proBtn = document.getElementById('checkout-pro');
  if (starterBtn) starterBtn.addEventListener('click', function(){ startCheckout('starter'); });
  if (proBtn) proBtn.addEventListener('click', function(){ startCheckout('pro'); });
});