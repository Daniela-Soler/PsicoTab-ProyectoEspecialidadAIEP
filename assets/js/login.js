document.addEventListener('DOMContentLoaded', () => {
    const btnToggle = document.getElementById('btn-toggle-password');
    const inputPassword = document.getElementById('password');
    const iconToggle = document.getElementById('icon-toggle-password');

    if (btnToggle && inputPassword && iconToggle) {
        btnToggle.addEventListener('click', () => {
            const isPassword = inputPassword.getAttribute('type') === 'password';
            inputPassword.setAttribute('type', isPassword ? 'text' : 'password');
            iconToggle.classList.toggle('bi-eye', !isPassword);
            iconToggle.classList.toggle('bi-eye-slash', isPassword);
        });
    }

    const linkOlvido = document.getElementById('link-olvido-clave');
    if (linkOlvido) {
        linkOlvido.addEventListener('click', (e) => {
            e.preventDefault();
            alert('Consulte con la administración técnica para el restablecimiento de su clave institucional.');
        });
    }

    const formLogin = document.getElementById('form-login');
    const inputEmail = document.getElementById('usuario');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (formLogin && inputEmail) {
        inputEmail.addEventListener('input', () => {
            const valor = inputEmail.value.trim();
            inputEmail.setCustomValidity(emailRegex.test(valor) ? '' : 'Formato de correo inválido');
        });

        formLogin.addEventListener('submit', (e) => {
            const valor = inputEmail.value.trim();
            inputEmail.setCustomValidity(emailRegex.test(valor) ? '' : 'Formato de correo inválido');

            if (!formLogin.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                const btnSubmit = document.getElementById('btn-submit-login');
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Verificando credenciales...`;
            }
            formLogin.classList.add('was-validated');
        });
    }
});