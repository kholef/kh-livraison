// KH LIVRAISON - Contact JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Envoi en cours...';
            
            try {
                const response = await fetch('php/contact.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ ' + data.message);
                    this.reset();
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('❌ Une erreur est survenue lors de l\'envoi du message');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Envoyer le message <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M18 2L9 11M18 2l-6 16-3-7-7-3 16-6z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }
        });
    }
});
