<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PsicoTab - Evaluación Clínica</title>
    <!-- Bootstrap 5 CSS e Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos Globales Centralizados -->
    <link href="../assets/css/styles.css" rel="stylesheet">
    <style>
        .test-selector-card {
            border: 2px solid var(--psico-card-border);
            border-radius: 12px;
            padding: 1rem;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            background: #ffffff;
        }
        .test-selector-card:hover {
            border-color: var(--psico-teal);
            box-shadow: 0 4px 10px rgba(27, 166, 166, 0.1);
        }
        .btn-check:checked + .test-selector-card {
            border-color: var(--psico-teal);
            background-color: rgba(27, 166, 166, 0.06);
        }
    </style>
</head>
<body>

    <!-- Inclusión de la Navbar Compartida -->
    <?php include 'partials/navbar.php'; ?>

    <div class="container pb-5">
        <!-- Pestañas (Wizard Dinámico) -->
        <ul class="nav nav-pills nav-pills-psicotab mb-4 justify-content-center" id="evaluacionTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="paciente-tab" data-bs-toggle="pill" data-bs-target="#tab-paciente" type="button" role="tab">
                    <i class="bi bi-person-vcard me-1"></i> 1. Paciente
                </button>
            </li>
            <li class="nav-item tab-instrumento" id="nav-item-phq9">
                <button class="nav-link" id="phq9-tab" data-bs-toggle="pill" data-bs-target="#tab-phq9" type="button" role="tab">
                    <i class="bi bi-clipboard2-pulse me-1"></i> 2. PHQ-9 (Depresión)
                </button>
            </li>
            <li class="nav-item tab-instrumento" id="nav-item-gad7">
                <button class="nav-link" id="gad7-tab" data-bs-toggle="pill" data-bs-target="#tab-gad7" type="button" role="tab">
                    <i class="bi bi-activity me-1"></i> 3. GAD-7 (Ansiedad)
                </button>
            </li>
            <li class="nav-item tab-instrumento" id="nav-item-asrs">
                <button class="nav-link" id="asrs-tab" data-bs-toggle="pill" data-bs-target="#tab-asrs" type="button" role="tab">
                    <i class="bi bi-lightning-charge me-1"></i> 4. ASRS (TDAH)
                </button>
            </li>
            <li class="nav-item tab-instrumento" id="nav-item-whodas">
                <button class="nav-link" id="whodas-tab" data-bs-toggle="pill" data-bs-target="#tab-whodas" type="button" role="tab">
                    <i class="bi bi-universal-access me-1"></i> 5. WHODAS 2.0 (Funcional)
                </button>
            </li>
        </ul>

        <!-- Formulario General hacia el Backend -->
        <form id="form-evaluacion" method="POST" action="../controllers/EvaluacionController.php" novalidate>
            <div class="tab-content" id="evaluacionTabContent">
                
                <!-- PESTAÑA 1: DATOS PACIENTE Y SELECCIÓN DE INSTRUMENTOS -->
                <div class="tab-pane fade show active" id="tab-paciente" role="tabpanel">
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-dark"><i class="bi bi-person-lines-fill text-primary me-2"></i>Ficha de Identificación</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="paciente_rut" class="form-label fw-bold">RUT / Identificador</label>
                                <input type="text" class="form-control form-control-lg" id="paciente_rut" name="paciente_rut" placeholder="12.345.678-K" required autocomplete="off">
                                <div id="rut-feedback" class="invalid-feedback"></div>
                            </div>
                            <div class="col-md-6">
                                <label for="paciente_nombre" class="form-label fw-bold">Nombre Completo o Código Seudónimo</label>
                                <input type="text" class="form-control form-control-lg" id="paciente_nombre" name="paciente_nombre" placeholder="Ej: Paciente A" required>
                            </div>
                            <div class="col-md-4">
                                <label for="paciente_nacimiento" class="form-label fw-bold">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="paciente_nacimiento" name="paciente_nacimiento" required>
                            </div>
                            <div class="col-md-4">
                                <label for="paciente_sexo" class="form-label fw-bold">Sexo Registral</label>
                                <select class="form-select" id="paciente_sexo" name="paciente_sexo" required>
                                    <option value="" disabled selected>Seleccione opción...</option>
                                    <option value="F">Femenino</option>
                                    <option value="M">Masculino</option>
                                    <option value="O">Otro / No declara</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="paciente_motivo" class="form-label fw-bold">Contexto Institucional</label>
                                <select class="form-select" id="paciente_motivo" name="paciente_motivo">
                                    <option value="atencion_primaria">Atención Primaria (CESFAM / COSAM)</option>
                                    <option value="educacional">Programa Integración / Educacional</option>
                                    <option value="consulta_privada">Consulta Particular</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Selector de Pruebas a Aplicar -->
                    <div class="card border-0 shadow-sm rounded-4 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-ui-checks text-primary me-2"></i>Baterías a Aplicar en esta Sesión</h5>
                                <p class="text-muted small mb-0">Seleccione los instrumentos que evaluará hoy. Las pestañas se adaptarán dinámicamente.</p>
                            </div>
                            <span class="badge bg-light text-dark border px-3 py-2" id="contador-pruebas">4 seleccionadas</span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <input type="checkbox" class="btn-check check-instrumento" id="check_phq9" name="instrumentos_activos[]" value="phq9" checked>
                                <label class="test-selector-card d-block h-100" for="check_phq9">
                                    <div class="d-flex justify-content-between mb-2">
                                        <i class="bi bi-clipboard2-pulse fs-4 text-primary"></i>
                                        <span class="badge bg-primary-subtle text-primary">9 ítems</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">PHQ-9</h6>
                                    <p class="text-muted small mb-0">Depresión y alerta autolesiva.</p>
                                </label>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <input type="checkbox" class="btn-check check-instrumento" id="check_gad7" name="instrumentos_activos[]" value="gad7" checked>
                                <label class="test-selector-card d-block h-100" for="check_gad7">
                                    <div class="d-flex justify-content-between mb-2">
                                        <i class="bi bi-activity fs-4 text-success"></i>
                                        <span class="badge bg-success-subtle text-success">7 ítems</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">GAD-7</h6>
                                    <p class="text-muted small mb-0">Tamizaje sintomatología ansiosa.</p>
                                </label>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <input type="checkbox" class="btn-check check-instrumento" id="check_asrs" name="instrumentos_activos[]" value="asrs" checked>
                                <label class="test-selector-card d-block h-100" for="check_asrs">
                                    <div class="d-flex justify-content-between mb-2">
                                        <i class="bi bi-lightning-charge fs-4 text-warning"></i>
                                        <span class="badge bg-warning-subtle text-dark">6 ítems</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">ASRS v1.1</h6>
                                    <p class="text-muted small mb-0">Cribado de TDAH en adultos.</p>
                                </label>
                            </div>

                            <div class="col-md-6 col-lg-3">
                                <input type="checkbox" class="btn-check check-instrumento" id="check_whodas" name="instrumentos_activos[]" value="whodas" checked>
                                <label class="test-selector-card d-block h-100" for="check_whodas">
                                    <div class="d-flex justify-content-between mb-2">
                                        <i class="bi bi-universal-access fs-4 text-info"></i>
                                        <span class="badge bg-info-subtle text-info">12 ítems</span>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-dark">WHODAS 2.0</h6>
                                    <p class="text-muted small mb-0">Evaluación de discapacidad (CIF).</p>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="button" class="btn btn-psico-primary" id="btn-iniciar-pruebas">
                                Iniciar Evaluación <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PESTAÑA 2: PHQ-9 (9 ÍTEMS COMPLETOS) -->
                <div class="tab-pane fade" id="tab-phq9" role="tabpanel">
                    <div class="alert alert-info border-0 shadow-sm mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i><strong>PHQ-9:</strong> Durante las últimas 2 semanas, ¿con qué frecuencia le han molestado los siguientes problemas?
                    </div>

                    <?php
                    $items_phq9 = [
                        1 => "Poco interés o alegría por hacer las cosas.",
                        2 => "Sensación de estar decaído/a, deprimido/a o desesperanzado/a.",
                        3 => "Dificultad para conciliar el sueño, continuar durmiendo o dormir demasiado.",
                        4 => "Sensación de cansancio o de tener poca energía.",
                        5 => "Poco apetito o comer en exceso.",
                        6 => "Sentirse mal con uno/a mismo/a (sentir que es un fracaso o que ha decepcionado a su familia).",
                        7 => "Dificultad para concentrarse en cosas tales como leer el periódico o ver televisión.",
                        8 => "Moverse o hablar tan lentamente que los demás lo notan, o bien estar tan inquieto/a que se mueve mucho más de lo habitual."
                    ];
                    foreach ($items_phq9 as $num => $enunciado): ?>
                        <div class="question-card" id="q-phq9-<?= $num ?>">
                            <p class="fw-semibold mb-2"><span class="question-number"><?= $num ?></span> <?= $enunciado ?></p>
                            <div class="btn-group option-group w-100 flex-wrap" role="group">
                                <input type="radio" class="btn-check" name="phq9_<?= $num ?>" id="phq9_<?= $num ?>_0" value="0">
                                <label class="btn btn-outline-custom flex-grow-1" for="phq9_<?= $num ?>_0">Nunca (0)</label>
                                <input type="radio" class="btn-check" name="phq9_<?= $num ?>" id="phq9_<?= $num ?>_1" value="1">
                                <label class="btn btn-outline-custom flex-grow-1" for="phq9_<?= $num ?>_1">Varios días (1)</label>
                                <input type="radio" class="btn-check" name="phq9_<?= $num ?>" id="phq9_<?= $num ?>_2" value="2">
                                <label class="btn btn-outline-custom flex-grow-1" for="phq9_<?= $num ?>_2">Más de la mitad (2)</label>
                                <input type="radio" class="btn-check" name="phq9_<?= $num ?>" id="phq9_<?= $num ?>_3" value="3">
                                <label class="btn btn-outline-custom flex-grow-1" for="phq9_<?= $num ?>_3">Casi todos los días (3)</label>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Ítem 9 (Crítico) -->
                    <div class="question-card border-warning" id="q-phq9-9" style="background-color: #fffefb;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <p class="fw-semibold mb-0"><span class="question-number bg-warning text-dark">9</span> Pensamientos de que estaría mejor muerto/a o de lastimarse de alguna manera.</p>
                            <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-triangle-fill"></i> Ítem Crítico</span>
                        </div>
                        <div class="btn-group option-group w-100 flex-wrap" role="group">
                            <input type="radio" class="btn-check item-critico" name="phq9_9" id="phq9_9_0" value="0">
                            <label class="btn btn-outline-custom flex-grow-1" for="phq9_9_0">Para nada (0)</label>
                            <input type="radio" class="btn-check item-critico" name="phq9_9" id="phq9_9_1" value="1">
                            <label class="btn btn-outline-custom flex-grow-1" for="phq9_9_1">Varios días (1)</label>
                            <input type="radio" class="btn-check item-critico" name="phq9_9" id="phq9_9_2" value="2">
                            <label class="btn btn-outline-custom flex-grow-1" for="phq9_9_2">Más de la mitad (2)</label>
                            <input type="radio" class="btn-check item-critico" name="phq9_9" id="phq9_9_3" value="3">
                            <label class="btn btn-outline-custom flex-grow-1" for="phq9_9_3">Casi todos los días (3)</label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary" onclick="irATab('paciente-tab')"><i class="bi bi-arrow-left"></i> Volver a Datos</button>
                        <button type="button" class="btn btn-psico-primary btn-siguiente" data-current="phq9">Siguiente <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <!-- PESTAÑA 3: GAD-7 (7 ÍTEMS COMPLETOS) -->
                <div class="tab-pane fade" id="tab-gad7" role="tabpanel">
                    <div class="alert alert-info border-0 shadow-sm mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i><strong>GAD-7:</strong> Durante las últimas 2 semanas, ¿con qué frecuencia le han molestado los siguientes problemas?
                    </div>

                    <?php
                    $items_gad7 = [
                        1 => "Sentirse nervioso/a, intranquilo/a o con los nervios de punta.",
                        2 => "No poder parar o controlar las preocupaciones.",
                        3 => "Preocuparse demasiado por diferentes cosas.",
                        4 => "Dificultad para relajarse.",
                        5 => "Estar tan inquieto/a que es difícil permanecer sentado/a.",
                        6 => "Irritarse o molestarse fácilmente.",
                        7 => "Sentir miedo como si algo terrible fuera a suceder."
                    ];
                    foreach ($items_gad7 as $num => $enunciado): ?>
                        <div class="question-card" id="q-gad7-<?= $num ?>">
                            <p class="fw-semibold mb-2"><span class="question-number"><?= $num ?></span> <?= $enunciado ?></p>
                            <div class="btn-group option-group w-100 flex-wrap" role="group">
                                <input type="radio" class="btn-check" name="gad7_<?= $num ?>" id="gad7_<?= $num ?>_0" value="0">
                                <label class="btn btn-outline-custom flex-grow-1" for="gad7_<?= $num ?>_0">Nunca (0)</label>
                                <input type="radio" class="btn-check" name="gad7_<?= $num ?>" id="gad7_<?= $num ?>_1" value="1">
                                <label class="btn btn-outline-custom flex-grow-1" for="gad7_<?= $num ?>_1">Varios días (1)</label>
                                <input type="radio" class="btn-check" name="gad7_<?= $num ?>" id="gad7_<?= $num ?>_2" value="2">
                                <label class="btn btn-outline-custom flex-grow-1" for="gad7_<?= $num ?>_2">Más de la mitad (2)</label>
                                <input type="radio" class="btn-check" name="gad7_<?= $num ?>" id="gad7_<?= $num ?>_3" value="3">
                                <label class="btn btn-outline-custom flex-grow-1" for="gad7_<?= $num ?>_3">Casi todos los días (3)</label>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-anterior" data-current="gad7"><i class="bi bi-arrow-left"></i> Anterior</button>
                        <button type="button" class="btn btn-psico-primary btn-siguiente" data-current="gad7">Siguiente <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <!-- PESTAÑA 4: ASRS v1.1 (6 ÍTEMS PARTE A OMS) -->
                <div class="tab-pane fade" id="tab-asrs" role="tabpanel">
                    <div class="alert alert-info border-0 shadow-sm mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i><strong>ASRS v1.1 (Cribado en Adultos):</strong> Responda pensando en los últimos 6 meses.
                    </div>

                    <?php
                    $items_asrs = [
                        1 => "¿Con qué frecuencia tiene dificultad para terminar los detalles finales de un proyecto una vez que las partes desafiantes han sido resueltas?",
                        2 => "¿Con qué frecuencia tiene dificultad para poner las cosas en orden cuando tiene que hacer una tarea que requiere organización?",
                        3 => "¿Con qué frecuencia tiene problemas para recordar citas u obligaciones?",
                        4 => "Cuando tiene una tarea que requiere pensar mucho, ¿con qué frecuencia evita o retrasa comenzarla?",
                        5 => "¿Con qué frecuencia mueve o retuerce las manos o los pies cuando tiene que permanecer sentado/a por mucho tiempo?",
                        6 => "¿Con qué frecuencia se siente demasiado activo/a y obligado/a a hacer cosas, como si tuviera un motor adentro?"
                    ];
                    foreach ($items_asrs as $num => $enunciado): ?>
                        <div class="question-card" id="q-asrs-<?= $num ?>">
                            <p class="fw-semibold mb-2"><span class="question-number"><?= $num ?></span> <?= $enunciado ?></p>
                            <div class="btn-group option-group w-100 flex-wrap" role="group">
                                <input type="radio" class="btn-check" name="asrs_<?= $num ?>" id="asrs_<?= $num ?>_0" value="0">
                                <label class="btn btn-outline-custom flex-grow-1" for="asrs_<?= $num ?>_0">Nunca (0)</label>
                                <input type="radio" class="btn-check" name="asrs_<?= $num ?>" id="asrs_<?= $num ?>_1" value="1">
                                <label class="btn btn-outline-custom flex-grow-1" for="asrs_<?= $num ?>_1">Raras veces (1)</label>
                                <input type="radio" class="btn-check" name="asrs_<?= $num ?>" id="asrs_<?= $num ?>_2" value="2">
                                <label class="btn btn-outline-custom flex-grow-1" for="asrs_<?= $num ?>_2">A veces (2)</label>
                                <input type="radio" class="btn-check" name="asrs_<?= $num ?>" id="asrs_<?= $num ?>_3" value="3">
                                <label class="btn btn-outline-custom flex-grow-1" for="asrs_<?= $num ?>_3">A menudo (3)</label>
                                <input type="radio" class="btn-check" name="asrs_<?= $num ?>" id="asrs_<?= $num ?>_4" value="4">
                                <label class="btn btn-outline-custom flex-grow-1" for="asrs_<?= $num ?>_4">Muy a menudo (4)</label>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-anterior" data-current="asrs"><i class="bi bi-arrow-left"></i> Anterior</button>
                        <button type="button" class="btn btn-psico-primary btn-siguiente" data-current="asrs">Siguiente <i class="bi bi-arrow-right"></i></button>
                    </div>
                </div>

                <!-- PESTAÑA 5: WHODAS 2.0 (12 ÍTEMS COMPLETOS) -->
                <div class="tab-pane fade" id="tab-whodas" role="tabpanel">
                    <div class="alert alert-info border-0 shadow-sm mb-3">
                        <i class="bi bi-info-circle-fill me-2"></i><strong>WHODAS 2.0 (12 ítems):</strong> En los últimos 30 días, ¿cuánta dificultad ha tenido debido a su estado de salud para realizar las siguientes actividades?
                    </div>

                    <?php
                    $items_whodas = [
                        1 => "Estar de pie durante periodos prolongados (ej. 30 minutos).",
                        2 => "Cuidar de las responsabilidades de su casa.",
                        3 => "Aprender una nueva tarea (ej. cómo llegar a un sitio nuevo).",
                        4 => "Participar en actividades de la comunidad (ej. festejos, actividades religiosas u otras).",
                        5 => "¿Cuánto le ha afectado emocionalmente su condición de salud?",
                        6 => "Concentrarse en hacer algo durante diez minutos.",
                        7 => "Caminar una larga distancia (ej. un kilómetro).",
                        8 => "Lavarse o asearse el cuerpo entero.",
                        9 => "Vestirse.",
                        10 => "Relacionarse con personas que no conoce.",
                        11 => "Mantener una amistad.",
                        12 => "Su trabajo diario o sus actividades de estudio."
                    ];
                    foreach ($items_whodas as $num => $enunciado): ?>
                        <div class="question-card" id="q-whodas-<?= $num ?>">
                            <p class="fw-semibold mb-2"><span class="question-number"><?= $num ?></span> <?= $enunciado ?></p>
                            <div class="btn-group option-group w-100 flex-wrap" role="group">
                                <input type="radio" class="btn-check" name="whodas_<?= $num ?>" id="whodas_<?= $num ?>_1" value="1">
                                <label class="btn btn-outline-custom flex-grow-1" for="whodas_<?= $num ?>_1">1. Ninguna</label>
                                <input type="radio" class="btn-check" name="whodas_<?= $num ?>" id="whodas_<?= $num ?>_2" value="2">
                                <label class="btn btn-outline-custom flex-grow-1" for="whodas_<?= $num ?>_2">2. Leve</label>
                                <input type="radio" class="btn-check" name="whodas_<?= $num ?>" id="whodas_<?= $num ?>_3" value="3">
                                <label class="btn btn-outline-custom flex-grow-1" for="whodas_<?= $num ?>_3">3. Moderada</label>
                                <input type="radio" class="btn-check" name="whodas_<?= $num ?>" id="whodas_<?= $num ?>_4" value="4">
                                <label class="btn btn-outline-custom flex-grow-1" for="whodas_<?= $num ?>_4">4. Severa</label>
                                <input type="radio" class="btn-check" name="whodas_<?= $num ?>" id="whodas_<?= $num ?>_5" value="5">
                                <label class="btn btn-outline-custom flex-grow-1" for="whodas_<?= $num ?>_5">5. Extrema</label>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="button" class="btn btn-outline-secondary btn-anterior" data-current="whodas"><i class="bi bi-arrow-left"></i> Anterior</button>
                        <button type="button" class="btn btn-success btn-lg px-4 shadow btn-finalizar" onclick="enviarFormulario()">
                            <i class="bi bi-cpu-fill me-2"></i> Procesar Baremación y Reporte
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Modal de Alerta Clínica: Ítem 9 PHQ-9 -->
    <div class="modal fade" id="modalAlertaClinica" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-danger border-2">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-shield-fill-exclamation me-2"></i>ALERTA CLÍNICA: Riesgo Autolesivo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="mb-2 fw-semibold text-danger">Se ha seleccionado un indicador en el Ítem 9 del PHQ-9.</p>
                    <p class="text-muted small">
                        El sistema ha detectado puntaje positivo de ideación de autodaño o muerte. Recuerde que el software apoya el cálculo numérico; es deber del evaluador activar los protocolos de salud correspondientes (Ley N° 20.584).
                    </p>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-danger px-4" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/rut-validator.js"></script>
    <script>
        const ordenPruebas = ['phq9', 'gad7', 'asrs', 'whodas'];

        function getPruebasSeleccionadas() {
            const checks = document.querySelectorAll('.check-instrumento:checked');
            return Array.from(checks).map(c => c.value);
        }

        function actualizarTabsVisibles() {
            const seleccionadas = getPruebasSeleccionadas();
            document.getElementById('contador-pruebas').textContent = `${seleccionadas.length} seleccionada(s)`;

            ordenPruebas.forEach(p => {
                const navItem = document.getElementById(`nav-item-${p}`);
                if (navItem) {
                    navItem.style.display = seleccionadas.includes(p) ? 'block' : 'none';
                }
            });

            const btnIniciar = document.getElementById('btn-iniciar-pruebas');
            if (seleccionadas.length === 0) {
                btnIniciar.disabled = true;
                btnIniciar.textContent = 'Seleccione al menos 1 prueba';
            } else {
                btnIniciar.disabled = false;
                btnIniciar.innerHTML = `Iniciar Evaluación (${seleccionadas.length}) <i class="bi bi-arrow-right ms-1"></i>`;
            }
        }

        document.querySelectorAll('.check-instrumento').forEach(check => {
            check.addEventListener('change', actualizarTabsVisibles);
        });

        function irATab(tabId) {
            const triggerEl = document.getElementById(tabId);
            if (triggerEl) {
                const tabInstance = new bootstrap.Tab(triggerEl);
                tabInstance.show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        document.getElementById('btn-iniciar-pruebas').addEventListener('click', () => {
            const seleccionadas = getPruebasSeleccionadas();
            if (seleccionadas.length > 0) {
                irATab(`${seleccionadas[0]}-tab`);
            }
        });

        document.querySelectorAll('.btn-siguiente').forEach(btn => {
            btn.addEventListener('click', () => {
                const current = btn.getAttribute('data-current');
                const seleccionadas = getPruebasSeleccionadas();
                const currentIndex = seleccionadas.indexOf(current);

                if (currentIndex !== -1 && currentIndex < seleccionadas.length - 1) {
                    const siguiente = seleccionadas[currentIndex + 1];
                    irATab(`${siguiente}-tab`);
                } else {
                    enviarFormulario();
                }
            });
        });

        document.querySelectorAll('.btn-anterior').forEach(btn => {
            btn.addEventListener('click', () => {
                const current = btn.getAttribute('data-current');
                const seleccionadas = getPruebasSeleccionadas();
                const currentIndex = seleccionadas.indexOf(current);

                if (currentIndex > 0) {
                    const anterior = seleccionadas[currentIndex - 1];
                    irATab(`${anterior}-tab`);
                } else {
                    irATab('paciente-tab');
                }
            });
        });

        document.querySelectorAll('.item-critico').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (parseInt(e.target.value, 10) > 0) {
                    const alertModal = new bootstrap.Modal(document.getElementById('modalAlertaClinica'));
                    alertModal.show();
                }
            });
        });

        function enviarFormulario() {
            const form = document.getElementById('form-evaluacion');
            const btns = document.querySelectorAll('.btn-finalizar');
            btns.forEach(b => {
                b.disabled = true;
                b.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span> Procesando...`;
            });
            form.submit();
        }

        actualizarTabsVisibles();
    </script>
</body>
</html>