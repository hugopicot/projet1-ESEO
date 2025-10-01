// Ouvre la fenêtre de contact (rend l'overlay visible)
function openPopup() {
    document.getElementById("overlay").classList.add("active");
}

// Ferme la fenêtre de contact (clic overlay ou bouton)
function closePopup(event) {
    // Ferme si on clique sur l'overlay ou sur le bouton X
    if (!event || event.target.id === "overlay") {
        document.getElementById("overlay").classList.remove("active");
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Gestion du formulaire de liste d'attente (waitlist)
    // But: prendre l'email, le vérifier simplement, l'envoyer via fetch,
    // puis afficher un message clair à l'utilisateur.
    function initWaitlistForm() {
        const form = document.getElementById('waitlist-form');
        if (!form) return; // Pas de formulaire sur la page principale

        const emailInput = document.getElementById('email');
        const submitBtn = form.querySelector('.join-btn');
        const originalText = submitBtn.textContent;

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = emailInput.value.trim();
            if (!isValidEmail(email)) {
                showNotification('Veuillez entrer une adresse email valide', 'error');
                return;
            }

            // Indiquer un traitement en cours (evite les doubles clics)
            submitBtn.textContent = 'Inscription...';
            submitBtn.disabled = true;

            try {
                // Ne pas modifier le fetch
                const response = await fetch('https://dummyjson.com/users/add', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email })
                });
                if (!response.ok) throw new Error('bad status');
                const data = await response.json();
                // Succès: affichage d'une notification de confirmation
                showNotification('Merci ! Inscription confirmee (id ' + data.id + ').', 'success');
                form.reset();
            } catch (err) {
                // Erreur réseau/serveur
                showNotification('Une erreur est survenue. Reessayez.', 'error');
            } finally {
                // Restaurer le bouton à son état initial
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // Validation simple: on verifie juste "@" et un point
    function isValidEmail(text) {
        return text.indexOf('@') > 0 && text.indexOf('.') > 0;
    }

    // Notification tres simple (toujours visible 4s)
    function showNotification(message, type) {
        var old = document.querySelector('.notification');
        if (old) old.remove();
        var box = document.createElement('div');
        box.className = 'notification ' + (type || 'info');
        box.textContent = message;
        box.setAttribute('role', 'status');
        box.setAttribute('aria-live', 'polite');
        box.style.position = 'fixed';
        box.style.top = '16px';
        box.style.right = '16px';
        box.style.padding = '12px 16px';
        box.style.borderRadius = '10px';
        box.style.font = '500 14px Manrope, sans-serif';
        box.style.color = '#000';
        box.style.zIndex = '10000';
        box.style.background = type === 'success' ? '#8CFF2E' : (type === 'error' ? '#FF4D4F' : '#2196F3');
        document.body.appendChild(box);
        setTimeout(function () { box.remove(); }, 4000);
    }

    // Initialiser le formulaire si présent
    initWaitlistForm();

    // (Pas d'animations superflues pour rester simple)

    // Surlignage du lien de navigation actif (après chargement du DOM)
    // Références utilisées pour surligner l'onglet actif au scroll
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop - sectionHeight / 4) {
                current = section.getAttribute('id') || '';
            }
        });
        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === '#' + current) {
                link.classList.add('active');
            }
        });
    });
});


// (Surlignage nav déplacé dans DOMContentLoaded)

// Fonction pour gérer l'ouverture/fermeture des éléments FAQ
function toggleFaqItem(id) {
    const content = document.getElementById(`faq-content-${id}`);
    const button = document.getElementById(`faq-button-${id}`);
    const iconPlus = document.getElementById(`faq-icon-plus-${id}`);
    const iconMinus = document.getElementById(`faq-icon-minus-${id}`);

    if (content && button && iconPlus && iconMinus) {
        const isExpanded = button.getAttribute('aria-expanded') === 'true';

        if (isExpanded) {
            // Fermer
            content.style.maxHeight = '0';
            button.setAttribute('aria-expanded', 'false');
            iconPlus.classList.remove('hidden');
            iconMinus.classList.add('hidden');
        } else {
            // Ouvrir
            content.style.maxHeight = content.scrollHeight + 'px';
            button.setAttribute('aria-expanded', 'true');
            iconPlus.classList.add('hidden');
            iconMinus.classList.remove('hidden');
        }
    }
}
