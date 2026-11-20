<?php
session_start();

if (!isset($_SESSION['id_evaluador'])) {
    header('Location: login.php');
    exit();
}

$nombre_evaluador = $_SESSION['nombre_evaluador'] ?? 'Evaluador Clínico';
$rol_evaluador    = $_SESSION['evaluador']['rol'] ?? 'Evaluador Clínico';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoTab - Panel de Control</title>
    <!-- Bootstrap 5 CSS e Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>

    <!-- Navbar Compartida -->
    <?php include 'partials/navbar.php'; ?>

    <div class="container pb-5">
        <!-- Saludo y Bienvenida -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        Bienvenido/a, <?= htmlspecialchars($nombre_evaluador) ?>
                    </h4>
                    <p class="text-muted small mb-0">
                        Sesión activa | <?= htmlspecialchars($rol_evaluador) ?> | Entorno seguro bajo Ley N° 19.628
                    </p>
                </div>
                <div>
                    <a href="evaluacion.php" class="btn btn-psico-primary btn-lg shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Nueva Evaluación
                    </a>
                </div>
            </div>
        </div>

        <!-- Métricas Rápidas (Cards) -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 bg-primary-subtle text-primary me-3">
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Evaluaciones Realizadas</span>
                            <h3 class="fw-bold mb-0 text-dark" id="stat-total-evaluaciones">-</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 bg-warning-subtle text-warning me-3">
                            <i class="bi bi-shield-fill-exclamation fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Alertas Críticas (Ítem 9)</span>
                            <h3 class="fw-bold mb-0 text-dark" id="stat-alertas-criticas">-</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-3 bg-success-subtle text-success me-3">
                            <i class="bi bi-check-circle-fill fs-4"></i>
                        </div>
                        <div>
                            <span class="text-muted small d-block">Baterías Baremadas</span>
                            <h3 class="fw-bold mb-0 text-dark">100%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Tabla de Evaluaciones Recientes -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
                <div>
                    <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Registros Recientes</h6>
                    <span class="text-muted small">Filtra por RUT o nombre del evaluado</span>
                </div>
                
                <!-- Buscador por RUT o Nombre -->
                <div class="input-group" style="max-width: 340px;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="filtro-registros" name="buscar" class="form-control border-start-0" placeholder="Buscar RUT o nombre...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabla-registros">
                    <thead class="table-light text-muted small">
                        <tr>
                            <th>Identificador</th>
                            <th>Paciente</th>
                            <th>Fecha</th>
                            <th>Baterías Aplicadas</th>
                            <th>Alerta Ítem 9</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody class="small" id="tbody-registros">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <span class="spinner-border spinner-border-sm me-2"></span>Cargando registros...
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Bootstrap 5 JS -->
    <script src="../assets/js/dashboard.js"></script>
</body>
</html>