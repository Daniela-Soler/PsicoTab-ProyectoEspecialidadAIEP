<?php
session_start();
if (!isset($_SESSION['id_evaluador'])) {
    header('Location: login.php');
    exit();
}
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
                <a href="dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Dashboard
                </a>
                <button class="btn btn-outline-secondary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Imprimir / PDF
                </button>
                <a href="evaluacion.php" class="btn btn-psico-primary">
                    <i class="bi bi-plus-circle me-1"></i> Nueva Evaluación
                </a>
            </div>
        </div>

        <!-- Alerta de error si falla la carga -->
        <div id="alerta-error-informe" class="alert alert-danger d-none mb-4" role="alert"></div>

        <!-- Banner de Alerta Crítica (Visible solo si Ítem 9 PHQ-9 > 0) -->
        <div id="banner-alerta-critica" class="alerta-riesgo-banner mb-4 d-none">
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

        <!-- Ficha de Datos del Paciente -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-person-badge text-primary me-2"></i>Datos del Paciente Evaluado</h6>
            <div class="row g-3 small">
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Identificador / RUT:</span>
                    <strong class="text-dark fs-6" id="res-rut">Cargando...</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Nombre / Código:</span>
                    <strong class="text-dark fs-6" id="res-nombre">-</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Fecha y Hora:</span>
                    <strong class="text-dark" id="res-fecha">-</strong>
                </div>
                <div class="col-6 col-md-3">
                    <span class="text-muted d-block">Contexto de Aplicación:</span>
                    <strong class="text-dark" id="res-contexto">-</strong>
                </div>
            </div>
        </div>

        <!-- Grilla de Resultados: Tarjetas Modulares por Test -->
        <div class="row g-4 mb-4" id="grilla-resultados">

            <!-- Tarjeta 1: PHQ-9 (Depresión) -->
            <div class="col-md-6 col-lg-3 d-none" id="card-phq9">
                <div class="card-resultado h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary-subtle text-primary fw-bold">PHQ-9</span>
                        <i class="bi bi-clipboard2-pulse fs-5 text-primary"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display" id="phq9-puntaje">-</div>
                        <div class="puntaje-total-label">/ 27 pts</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad d-inline-block" id="phq9-nivel">-</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar" id="phq9-barra" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <p class="text-muted small mb-0" id="phq9-desc">Evaluación de sintomatología depresiva en las últimas 2 semanas.</p>
                </div>
            </div>

            <!-- Tarjeta 2: GAD-7 (Ansiedad) -->
            <div class="col-md-6 col-lg-3 d-none" id="card-gad7">
                <div class="card-resultado h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-success-subtle text-success fw-bold">GAD-7</span>
                        <i class="bi bi-activity fs-5 text-success"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display" id="gad7-puntaje">-</div>
                        <div class="puntaje-total-label">/ 21 pts</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad d-inline-block" id="gad7-nivel">-</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar" id="gad7-barra" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <p class="text-muted small mb-0" id="gad7-desc">Tamizaje de sintomatología ansiosa generalizada.</p>
                </div>
            </div>

            <!-- Tarjeta 3: ASRS v1.1 (TDAH Adultos) -->
            <div class="col-md-6 col-lg-3 d-none" id="card-asrs">
                <div class="card-resultado h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-warning-subtle text-dark fw-bold">ASRS v1.1</span>
                        <i class="bi bi-lightning-charge fs-5 text-warning"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display" id="asrs-puntaje">-</div>
                        <div class="puntaje-total-label">/ 6 reactivos</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad d-inline-block" id="asrs-nivel">-</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar" id="asrs-barra" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <p class="text-muted small mb-0" id="asrs-desc">Cribado de TDAH en adultos (Parte A OMS).</p>
                </div>
            </div>

            <!-- Tarjeta 4: WHODAS 2.0 (Discapacidad / CIF) -->
            <div class="col-md-6 col-lg-3 d-none" id="card-whodas">
                <div class="card-resultado h-100">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-info-subtle text-info fw-bold">WHODAS 2.0</span>
                        <i class="bi bi-universal-access fs-5 text-info"></i>
                    </div>
                    <div class="d-flex align-items-baseline gap-2 mb-2">
                        <div class="puntaje-display" id="whodas-puntaje">-</div>
                        <div class="puntaje-total-label">Discapacidad</div>
                    </div>
                    <div class="mb-3">
                        <span class="badge-severidad d-inline-block" id="whodas-nivel">-</span>
                    </div>
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar" id="whodas-barra" role="progressbar" style="width: 0%;"></div>
                    </div>
                    <p class="text-muted small mb-0" id="whodas-desc">Evaluación de limitación funcional según modelo CIF de la OMS.</p>
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

            <div id="alerta-observacion" class="alert py-2 px-3 small d-none" role="alert"></div>

            <form id="form-observaciones">
                <div class="mb-3">
                    <textarea class="form-control" id="texto-observaciones" name="observaciones" rows="3" placeholder="Añada apreciaciones diagnósticas, derivaciones sugeridas o notas de seguimiento en base a los puntajes obtenidos..."></textarea>
                </div>
                
                <div class="d-flex justify-content-end no-print">
                    <button type="submit" id="btn-guardar-obs" class="btn btn-psico-primary btn-sm px-4">
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/resultados.js"></script>
</body>
</html>