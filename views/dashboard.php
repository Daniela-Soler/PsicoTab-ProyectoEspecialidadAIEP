<?php
session_start();
$evaluador = $_SESSION['evaluador'] ?? [
    'nombre' => 'Evaluador Invitado',
    'rol' => 'Psicólogo Clínico'
];
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
                        Bienvenido/a, <?= htmlspecialchars($evaluador['nombre']) ?>
                    </h4>
                    <p class="text-muted small mb-0">
                        Sesión activa | <?= htmlspecialchars($evaluador['rol']) ?> | Entorno seguro bajo Ley N° 19.628
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
                            <h3 class="fw-bold mb-0 text-dark">18</h3>
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
                            <h3 class="fw-bold mb-0 text-dark">2</h3>
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
                    <tbody class="small">
                        <tr>
                            <td class="col-rut"><code>12.345.678-K</code></td>
                            <td class="col-nombre">P. González R.</td>
                            <td>Hoy, 10:15</td>
                            <td><span class="badge bg-secondary-subtle text-dark">PHQ-9, GAD-7, ASRS, WHODAS</span></td>
                            <td><span class="badge bg-danger">Positiva</span></td>
                            <td class="text-end">
                                <a href="resultados.php" class="btn btn-sm btn-outline-primary">Ver Informe</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-rut"><code>18.765.432-1</code></td>
                            <td class="col-nombre">M. Silva T.</td>
                            <td>Ayer, 16:30</td>
                            <td><span class="badge bg-secondary-subtle text-dark">PHQ-9, GAD-7</span></td>
                            <td><span class="badge bg-success-subtle text-success">Negativa</span></td>
                            <td class="text-end">
                                <a href="resultados.php" class="btn btn-sm btn-outline-primary">Ver Informe</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('filtro-registros').addEventListener('keyup', function() {
            const termino = this.value.toLowerCase().trim();
            const filas = document.querySelectorAll('#tabla-registros tbody tr');

            filas.forEach(fila => {
                const rut = fila.querySelector('.col-rut').textContent.toLowerCase();
                const nombre = fila.querySelector('.col-nombre').textContent.toLowerCase();
                fila.style.display = (rut.includes(termino) || nombre.includes(termino)) ? '' : 'none';
            });
        });
    </script>
</body>
</html>