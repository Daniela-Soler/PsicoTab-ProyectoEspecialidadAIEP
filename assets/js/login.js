const btnToggle = document.getElementById('btn-toggle-password');
const inputPassword = document.getElementById('password');
const iconToggle = document.getElementById('icon-toggle-password');

btnToggle.addEventListener('click', () => {
    const isPassword = inputPassword.getAttribute('type') === 'password';
    inputPassword.setAttribute('type', isPassword ? 'text' : 'password');
    iconToggle.classList.toggle('bi-eye', !isPassword);
    iconToggle.classList.toggle('bi-eye-slash', isPassword);
});

const formLogin = document.getElementById('form-login');
const inputEmail = document.getElementById('usuario');
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Validar formato estricto correo@dominio.ext mientras escribe
inputEmail.addEventListener('input', () => {
    const valor = inputEmail.value.trim();
    if (!emailRegex.test(valor)) {
        inputEmail.setCustomValidity('Formato de correo inválido');
    } else {
        inputEmail.setCustomValidity('');
    }
});

formLogin.addEventListener('submit', (e) => {
    // Verificar el formato antes de evaluar checkValidity()
    if (!emailRegex.test(inputEmail.value.trim())) {
        inputEmail.setCustomValidity('Formato de correo inválido');
    } else {
        inputEmail.setCustomValidity('');
    }

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