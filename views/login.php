<?php
session_start();
$error_login = $_SESSION['error_login'] ?? null;
unset($_SESSION['error_login']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoTab - Acceso al Sistema</title>
    <!-- Bootstrap 5 CSS e Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos Globales Centralizados -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body class="login-body-layout">

    <div class="login-card">
        <!-- Encabezado con Logotipo Oficial -->
        <div class="login-header">
            <img src="../assets/img/logo-psicotab.jpeg" alt="PsicoTab Baremación y Reportes" class="login-logo">
            <h5 class="fw-bold text-dark mb-1">Módulo de Autenticación</h5>
            <p class="text-muted small mb-0">Registro y Baremación Psicométrica Estandarizada</p>
        </div>

        <div class="login-content">
            <!-- Alerta dinámica de error -->
            <?php if ($error_login): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 small" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2 flex-shrink-0"></i>
                    <div><?= htmlspecialchars($error_login) ?></div>
                    <button type="button" class="btn-close ms-auto p-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form id="form-login" method="POST" action="../controllers/LoginController.php" novalidate>
                <!-- Usuario / Correo -->
                <div class="form-floating mb-3">
                    <input type="text" 
                           class="form-control rounded-3" 
                           id="usuario" 
                           name="usuario" 
                           placeholder="nombre@institucion.cl" 
                           required 
                           autocomplete="username">
                    <label for="usuario"><i class="bi bi-person me-1"></i> Usuario o Correo Institucional</label>
                    <div class="invalid-feedback">Por favor ingrese su usuario o correo.</div>
                </div>

                <!-- Contraseña con botón de alternancia -->
                <div class="input-group mb-3">
                    <div class="form-floating flex-grow-1">
                        <input type="password" 
                               class="form-control rounded-start-3" 
                               id="password" 
                               name="password" 
                               placeholder="Contraseña" 
                               required 
                               autocomplete="current-password">
                        <label for="password"><i class="bi bi-key me-1"></i> Contraseña de Acceso</label>
                    </div>
                    <button class="btn btn-outline-secondary border rounded-end-3 px-3 d-flex align-items-center" 
                            type="button" 
                            id="btn-toggle-password" 
                            title="Mostrar u ocultar contraseña">
                        <i class="bi bi-eye" id="icon-toggle-password"></i>
                    </button>
                    <div class="invalid-feedback">Por favor ingrese su contraseña.</div>
                </div>

                <!-- Recordatorio y enlace -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="recordar" name="recordar">
                        <label class="form-check-label small text-muted" for="recordar">Recordar sesión</label>
                    </div>
                    <a href="#" class="small text-decoration-none" style="color: var(--psico-primary);" onclick="alert('Consulte con la administración técnica para el restablecimiento de su clave institucional.'); return false;">
                        ¿Olvidó su clave?
                    </a>
                </div>

                <!-- Botón de Envío -->
                <button type="submit" id="btn-submit-login" class="btn btn-psico-primary w-100 py-2 fs-6 shadow-sm mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión Segura
                </button>
            </form>

            <!-- Nota Legal de Confidencialidad -->
            <div class="legal-notice text-center">
                <i class="bi bi-shield-check text-success me-1"></i>
                <strong>Acceso Clínico Restringido:</strong> Plataforma sujeta a la <strong>Ley N° 19.628</strong> y <strong>Ley N° 20.584</strong> sobre custodia y confidencialidad de datos sensibles de salud.
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
        formLogin.addEventListener('submit', (e) => {
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
    </script>
</body>
</html>