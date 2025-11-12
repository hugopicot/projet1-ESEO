/*
   SCRIPT PRINCIPAL - Nova Landing Page
   ===================================
   Gestion des interactions utilisateur pour la page d'accueil Nova.
   Fonctionnalités : popup de contact, navigation active, FAQ.
   Technologies : Vanilla JavaScript ES6+
*/

/*
   GESTION DU POPUP DE CONTACT
   ===========================
   Fonctions pour ouvrir/fermer le popup modal de contact
*/

function openPopup() {
    const popup = document.getElementById('contactPopup');
    if (!popup) return;
    popup.style.display = 'flex';
    popup.classList.add('show');
}

function closePopup() {
    const popup = document.getElementById('contactPopup');
    if (!popup) return;
    popup.style.display = 'none';
    popup.classList.remove('show');
}

/*
   GESTION DE LA NAVIGATION ACTIVE
   ===============================
   Met en surbrillance le lien de navigation actif en fonction du scroll
*/

document.addEventListener('DOMContentLoaded', function() {
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

/*
   GESTION DE LA FAQ
   =================
   Ouverture et fermeture des questions/réponses FAQ
*/

function toggleFaqItem(id) {
    const content = document.getElementById(`faq-content-${id}`);
    const button = document.getElementById(`faq-button-${id}`);
    const iconPlus = document.getElementById(`faq-icon-plus-${id}`);
    const iconMinus = document.getElementById(`faq-icon-minus-${id}`);

    if (content && button && iconPlus && iconMinus) {
        const isExpanded = button.getAttribute('aria-expanded') === 'true';

        if (isExpanded) {
            content.style.maxHeight = '0';
            button.setAttribute('aria-expanded', 'false');
            iconPlus.classList.remove('hidden');
            iconMinus.classList.add('hidden');
        } else {
            content.style.maxHeight = content.scrollHeight + 'px';
            button.setAttribute('aria-expanded', 'true');
            iconPlus.classList.add('hidden');
            iconMinus.classList.remove('hidden');
        }
    }
}
