<?php
session_start();

// Simulación de datos recibidos del backend o controlador
$paciente = $_SESSION['paciente_resultado'] ?? [
    'rut' => '12.345.678-K',
    'nombre' => 'P. González R.',
    'edad' => '32 años',
    'fecha_evaluacion' => date('d/m/Y - H:i'),
    'contexto' => 'Atención Primaria (CESFAM / COSAM)'
];

// Indicador del Ítem 9 de PHQ-9 (alerta autolesiva)
$alerta_critica_phq9 = true; // Simulado para maqueta visual
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoTab - Ficha de Resultados</title>
    <!-- Bootstrap 5 CSS e Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos Centralizados -->
    <link href="../assets/css/styles.css" rel="stylesheet">
</head>
<body>

    <!-- Inclusión de la Navbar Compartida -->
    <?php include 'partials/navbar.php'; ?>

    <div class="container pb-5">

        <!-- Barra Superior: Encabezado y Acciones de Salida -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 no-print">
            <div>
                <h4 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-bar-graph text-primary me-2"></i>Informe Psicométrico Estandarizado</h4>
                <p class="text-muted small mb-0">Tabulación algorítmica conforme a directrices técnicas y de baremación.</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Imprimir / PDF
                </button>
                <a href="evaluacion.php" class="btn btn-psico-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Evaluación
                </a>
            </div>
        </div>

        <!-- Banner de Alerta Crítica (Reactivo según Ítem 9 PHQ-9) -->
        <?php if ($alerta_critica_phq9): ?>
            <div class="alerta-riesgo-banner mb-4">
                <div class="d-flex align-items-start">
                    <i class="bi bi-shield-fill-exclamation text-danger fs-3 me-3 mt-1"></i>
                    <div>
                        <h6 class="fw-bold text-danger mb-1">ALERTA CLÍNICA: Puntuación Positiva en Ítem 9 (PHQ-9)</h6>
                        <p class="mb-0 text-dark small">
                            El reactivo sobre pensamientos de muerte o autolesión registró respuesta activa. Conforme a las orientaciones del Ministerio de Salud (MINSAL) y la <strong>Ley N° 20.584</strong>, este puntaje exige contención y evaluación clínica presencial prioritaria, descartando un mero procesamiento computacional aislado.
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Ficha de Datos del Paciente -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge text-primary me-2"></i>Datos del Paciente Evaluado</h6>
            <div class="row g-3 small">
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Identificador / RUT:</span>
                    <strong class="text-dark fs-6"><?= htmlspecialchars($paciente['rut']) ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Nombre / Código:</span>
                    <strong class="text-dark fs-6"><?= htmlspecialchars($paciente['nombre']) ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Fecha y Hora:</span>
                    <strong class="text-dark"><?= htmlspecialchars($paciente['fecha_evaluacion']) ?></strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Contexto de Aplicación:</span>
                    <strong class="text-dark"><?= htmlspecialchars($paciente['contexto']) ?></strong>
                </div>
            </div>
        </div>

        <!-- Grilla de Resultados: Tarjetas Modulares por Test -->
        <div class="row g-4 mb-4">

            <!-- Tarjeta 1: PHQ-9 (Depresión) -->
            <div class="col-md-6 col-lg-3">
                <div class="card-resultado">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary-subtle text-primary fw-bold">PHQ-9</span>
                        <i class="bi bi-clipboard2-pulse fs-5 text-primary"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display">14</div>
                        <div class="puntaje-total-label">/ 27 pts</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad semaforo-moderado d-inline-block">Moderadamente Grave</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 52%;" aria-valuenow="14" aria-valuemin="0" aria-valuemax="27"></div>
                    </div>
                    <p class="text-muted small mb-0">
                        Puntaje indicativo de sintomatología depresiva manifiesta. Sugiere interconsulta y plan terapéutico activo.
                    </p>
                </div>
            </div>

            <!-- Tarjeta 2: GAD-7 (Ansiedad) -->
            <div class="col-md-6 col-lg-3">
                <div class="card-resultado">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success-subtle text-success fw-bold">GAD-7</span>
                        <i class="bi bi-activity fs-5 text-success"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display">8</div>
                        <div class="puntaje-total-label">/ 21 pts</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad semaforo-leve d-inline-block">Ansiedad Leve</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-info" role="progressbar" style="width: 38%;" aria-valuenow="8" aria-valuemin="0" aria-valuemax="21"></div>
                    </div>
                    <p class="text-muted small mb-0">
                        Sintomatología ansiosa de baja intensidad. Se sugiere monitoreo psicoeducativo preventivo.
                    </p>
                </div>
            </div>

            <!-- Tarjeta 3: ASRS v1.1 (TDAH Adultos) -->
            <div class="col-md-6 col-lg-3">
                <div class="card-resultado">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-warning-subtle text-dark fw-bold">ASRS v1.1</span>
                        <i class="bi bi-lightning-charge fs-5 text-warning"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display">5</div>
                        <div class="puntaje-total-label">/ 6 reactivos</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad semaforo-severo d-inline-block">Sospecha Positiva</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-danger" role="progressbar" style="width: 83%;" aria-valuenow="5" aria-valuemin="0" aria-valuemax="6"></div>
                    </div>
                    <p class="text-muted small mb-0">
                        Presenta 4 o más reactivos en zona de sospecha clínica. Amerita evaluación neuropsicológica detallada.
                    </p>
                </div>
            </div>

            <!-- Tarjeta 4: WHODAS 2.0 (Discapacidad / CIF) -->
            <div class="col-md-6 col-lg-3">
                <div class="card-resultado">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-info-subtle text-info fw-bold">WHODAS 2.0</span>
                        <i class="bi bi-universal-access fs-5 text-info"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display">24%</div>
                        <div class="puntaje-total-label">Discapacidad</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad semaforo-leve d-inline-block">Dificultad Leve</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-teal" role="progressbar" style="width: 24%;" aria-valuenow="24" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <p class="text-muted small mb-0">
                        Afectación moderada en áreas de participación y movilidad según el modelo CIF de la OMS.
                    </p>
                </div>
            </div>

        </div>

        <!-- Bloque de Observaciones Clínicas (Editable tras conocer resultados) -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold text-dark mb-0">
                    <i class="bi bi-pencil-square text-primary me-2"></i>Observaciones Clínicas y Síntesis del Evaluador
                </h6>
                <span class="badge bg-primary-subtle text-primary">Editable</span>
            </div>

            <?php if (isset($_SESSION['msg_observacion'])): ?>
                <div class="alert alert-success py-2 px-3 small alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= htmlspecialchars($_SESSION['msg_observacion']) ?>
                    <button type="button" class="btn-close p-2" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['msg_observacion']); ?>
            <?php endif; ?>

            <form method="POST" action="../controllers/EvaluacionController.php">
                <input type="hidden" name="accion" value="actualizar_observaciones">
                <input type="hidden" name="evaluacion_id" value="<?= htmlspecialchars($paciente['evaluacion_id'] ?? '1') ?>">
                
                <div class="mb-3">
                    <textarea class="form-control" name="observaciones" rows="3" placeholder="Añada apreciaciones diagnósticas, derivaciones sugeridas o notas de seguimiento en base a los puntajes obtenidos..."><?= htmlspecialchars($paciente['observaciones'] ?? 'Paciente colaboradora durante la aplicación. Refiere sobrecarga laboral en las últimas semanas.') ?></textarea>
                </div>
                
                <div class="d-flex justify-content-end no-print">
                    <button type="submit" class="btn btn-psico-primary btn-sm px-4">
                        <i class="bi bi-save me-1"></i> Guardar / Actualizar Observaciones
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Nota Técnica y Disclaimer Ético / Legal -->
        <div class="card border-0 shadow-sm p-4 bg-light rounded-4">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-info-circle text-primary me-2"></i>Consideraciones de Uso Clínico y Deontológico</h6>
            <p class="text-muted small mb-1">
                <strong>1. Apoyo al cómputo:</strong> Los valores aquí expresados corresponden a algoritmos de baremación computarizada y no constituyen por sí solos un diagnóstico definitivo. La formulación nosológica es responsabilidad exclusiva del profesional tratante debidamente acreditado.
            </p>
            <p class="text-muted small mb-0">
                <strong>2. Custodia de información sensible:</strong> Este informe contiene datos protegidos por la <strong>Ley N° 19.628</strong> sobre Protección de la Vida Privada y la <strong>Ley N° 20.584</strong> sobre Derechos y Deberes de las Personas en su Atención en Salud. Su difusión o almacenamiento no autorizado está sancionado por la legislación chilena vigente.
            </p>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>