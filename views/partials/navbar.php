<nav class="navbar navbar-expand-lg navbar-psicotab mb-4 py-2">
    <div class="container-fluid px-4">
        <!-- Logo Oficial PsicoTab -->
        <a class="navbar-brand d-flex align-items-center" href="../views/dashboard.php">
            <img src="../assets/img/logo-psicotab.jpeg" alt="PsicoTab Baremación y Reportes" class="brand-logo">
        </a>

        <!-- Indicadores de Seguridad y Usuario -->
        <div class="d-flex align-items-center gap-2">
            <span class="badge badge-ley py-2 px-3 rounded-pill d-none d-md-inline-block">
                <i class="bi bi-shield-check text-success me-1"></i> Ley N° 19.628 & 20.584
            </span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle border d-flex align-items-center py-1 px-3 rounded-pill" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle text-primary fs-5 me-2"></i>
                    <span class="small fw-semibold text-dark">Evaluador Clínico</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header">Sesión Activa</h6></li>
                    <li><a class="dropdown-item" href="../views/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="../controllers/LogoutController.php"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>