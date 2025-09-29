document.addEventListener('DOMContentLoaded', function() {
    
    // Gestion du formulaire de liste d'attente
    function initWaitlistForm() {
        const form = document.getElementById('waitlist-form');
        const emailInput = document.getElementById('email');
        const submitBtn = form?.querySelector('.join-btn');
        
        if (!form) return;
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = emailInput.value.trim();
            
            // Validation de l'email
            if (!isValidEmail(email)) {
                showNotification('Veuillez entrer une adresse email valide', 'error');
                return;
            }
            
            
            // Simuler une requête (remplacez par votre API)
            setTimeout(() => {
                // Succès
                showNotification('Merci ! Vous avez été ajouté à notre liste d\'attente.', 'success');
                form.reset();
                
                // Restaurer le bouton
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
                submitBtn.style.opacity = '1';
                
                // Animation de succès
                submitBtn.style.background = '#4CAF50';
                setTimeout(() => {
                    submitBtn.style.background = '';
                }, 2000);
                
            }, 2000);
        });
        
        // Animation au focus de l'input
        emailInput.addEventListener('focus', function() {
            this.parentElement.style.transform = 'scale(1.02)';
        });
        
        emailInput.addEventListener('blur', function() {
            this.parentElement.style.transform = 'scale(1)';
        });
    }
    
    // Validation d'email
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Système de notifications
    function showNotification(message, type = 'info') {
        // Supprimer les notifications existantes
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notif => notif.remove());
        
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        
        // Styles de base
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '20px',
            padding: '16px 24px',
            borderRadius: '12px',
            color: 'white',
            fontFamily: 'Manrope, sans-serif',
            fontWeight: '500',
            fontSize: '14px',
            zIndex: '10000',
            transform: 'translateX(100%)',
            transition: 'all 0.3s ease',
            maxWidth: '400px',
            boxShadow: '0 4px 20px rgba(0, 0, 0, 0.3)'
        });
        
        // Couleurs selon le type
        if (type === 'success') {
            notification.style.background = '#8CFF2E';
            notification.style.color = '#000000';
        } else if (type === 'error') {
            notification.style.background = '#FF4D4F';
            notification.style.color = '#000000';
        } else {
            notification.style.background = '#2196F3';
            notification.style.color = '#000000';
        }
        
        document.body.appendChild(notification);
        
        // Animation d'entrée
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Auto-suppression après 5 secondes
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
    }
    
    // Initialiser le formulaire
    initWaitlistForm();
    
    // Animation d'apparition du formulaire
    const formContainer = document.querySelector('.email-form-container');
    if (formContainer) {
        formContainer.style.opacity = '0';
        formContainer.style.transform = 'translateY(30px)';
        formContainer.style.transition = 'all 0.8s ease';
        
        setTimeout(() => {
            formContainer.style.opacity = '1';
            formContainer.style.transform = 'translateY(0)';
        }, 500);
    }
});