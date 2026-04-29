// KH LIVRAISON - Auth JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Tab Switching
    const authTabs = document.querySelectorAll('.auth-tab');
    const authForms = document.querySelectorAll('.auth-form');

    authTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            
            // Update tabs
            authTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            // Update forms
            authForms.forEach(form => {
                form.classList.remove('active');
                if (form.id === tabName + '-form') {
                    form.classList.add('active');
                }
            });
        });
    });

    // Login Form Submit
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Connexion...';
            
            try {
                const response = await fetch('php/login.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ ' + data.message);
                    window.location.href = 'livraisons.html';
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('❌ Une erreur est survenue lors de la connexion');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Se connecter <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4 10h12m0 0l-4-4m4 4l-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
            }
        });
    }

    // Register Form Submit
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = document.getElementById('register-password').value;
            const passwordConfirm = document.getElementById('register-password-confirm').value;
            
            if (password !== passwordConfirm) {
                alert('❌ Les mots de passe ne correspondent pas');
                return;
            }
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Inscription...';
            
            try {
                const response = await fetch('php/register.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('✅ ' + data.message);
                    window.location.href = 'livraisons.html';
                } else {
                    alert('❌ ' + data.message);
                }
            } catch (error) {
                console.error('Erreur:', error);
                alert('❌ Une erreur est survenue lors de l\'inscription');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Créer mon compte <svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4 10h12m0 0l-4-4m4 4l-4 4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>';
            }
        });
    }
});
