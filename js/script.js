/* 
   SCRIPT PRINCIPAL - Nova Landing Page
   ===================================
   Gestion des interactions utilisateur pour la page d'accueil Nova.
   Fonctionnalités : popup de contact, formulaire waitlist, notifications.
   Technologies : Vanilla JavaScript ES6+
*/

/* 
   GESTION DU POPUP DE CONTACT
   ===========================
   Fonctions pour ouvrir/fermer le popup modal de contact
*/

// Fonction pour ouvrir le popup de contact
function openPopup() {
    // Récupère l'élément popup par son ID
    const popup = document.getElementById('contactPopup');
    
    // Affiche le popup en changeant le style display
    popup.style.display = 'flex';
    
    // Ajoute une classe pour l'animation d'entrée (si définie en CSS)
    popup.classList.add('show');
}

// Fonction pour fermer le popup de contact
function closePopup() {
    // Récupère l'élément popup par son ID
    const popup = document.getElementById('contactPopup');
    
    // Cache le popup en changeant le style display
    popup.style.display = 'none';
    
    // Supprime la classe d'animation
    popup.classList.remove('show');
}

/* 
   GESTION DU FORMULAIRE WAITLIST
   ==============================
   Traitement de la soumission du formulaire d'inscription à la liste d'attente
*/

// Fonction de soumission du formulaire waitlist
function submitWaitlist() {
    // Récupère la valeur de l'input email et supprime les espaces
    const email = document.getElementById('email').value.trim();
    
    /* 
       VALIDATION EMAIL
       ================
       Vérifie que l'email est valide avec une regex
    */
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    // Vérifie si l'email est vide
    if (!email) {
        showNotification('Veuillez entrer votre adresse email.', 'error');
        return; // Arrête l'exécution si email vide
    }
    
    // Vérifie si l'email respecte le format attendu
    if (!emailRegex.test(email)) {
        showNotification('Veuillez entrer une adresse email valide.', 'error');
        return; // Arrête l'exécution si email invalide
    }

    /* 
       SIMULATION D'APPEL API
       =====================
       Simule l'envoi des données vers un serveur backend
    */
    
    // Affiche un message de chargement pendant le traitement
    showNotification('Inscription en cours...', 'info');
    
    // Simule un délai d'API avec setTimeout (2 secondes)
    setTimeout(() => {
        // Simule une réponse positive du serveur
        showNotification('Merci ! Vous avez été ajouté à notre liste d\'attente.', 'success');
        
        // Réinitialise le champ email après succès
        document.getElementById('email').value = '';
    }, 2000); // Délai de 2000ms = 2 secondes
}

/* 
   SYSTÈME DE NOTIFICATIONS
   ========================
   Affichage de messages temporaires à l'utilisateur
*/

// Fonction pour afficher une notification
function showNotification(message, type) {
    /* 
       CRÉATION DYNAMIQUE DE LA NOTIFICATION
       ====================================
       Crée un élément div pour contenir le message
    */
    const notification = document.createElement('div');
    
    // Définit le contenu textuel de la notification
    notification.textContent = message;
    
    // Ajoute les classes CSS de base pour le style
    notification.className = `notification ${type}`;
    
    /* 
       STYLES DYNAMIQUES SELON LE TYPE
       ===============================
       Applique des couleurs différentes selon le type de message
    */
    
    // Styles de base communs à toutes les notifications
    notification.style.cssText = `
        position: fixed;           /* Position fixe par rapport à la fenêtre */
        top: 20px;                /* Distance du haut de l'écran */
        right: 20px;              /* Distance du bord droit */
        padding: 15px 20px;       /* Espacement interne */
        border-radius: 5px;       /* Coins arrondis */
        color: white;             /* Texte blanc pour contraste */
        font-weight: bold;        /* Texte en gras */
        z-index: 10000;          /* Au-dessus de tous les autres éléments */
        opacity: 0;              /* Invisible au départ pour animation */
        transform: translateX(100%); /* Décalé vers la droite pour animation */
        transition: all 0.3s ease;  /* Animation fluide */
    `;
    
    // Couleurs spécifiques selon le type de notification
    switch(type) {
        case 'success':
            notification.style.backgroundColor = '#28a745'; // Vert pour succès
            break;
        case 'error':
            notification.style.backgroundColor = '#dc3545'; // Rouge pour erreur
            break;
        case 'info':
            notification.style.backgroundColor = '#17a2b8'; // Bleu pour info
            break;
        default:
            notification.style.backgroundColor = '#6c757d'; // Gris par défaut
    }
    
    /* 
       AFFICHAGE ET ANIMATION
       =====================
       Ajoute la notification au DOM et lance les animations
    */
    
    // Ajoute la notification au body de la page
    document.body.appendChild(notification);
    
    // Force le reflow pour que les styles soient appliqués
    notification.offsetHeight;
    
    // Animation d'entrée : rend visible et repositionne
    notification.style.opacity = '1';
    notification.style.transform = 'translateX(0)';
    
    /* 
       SUPPRESSION AUTOMATIQUE
       ======================
       Retire la notification après un délai
    */
    setTimeout(() => {
        // Animation de sortie
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        
        // Supprime l'élément du DOM après l'animation
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300); // Délai correspondant à la durée de transition CSS
    }, 3000); // Affichage pendant 3 secondes
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
