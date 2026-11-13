<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Caso 1: Actualización de observaciones desde resultados.php
    if (isset($_POST['accion']) && $_POST['accion'] === 'actualizar_observaciones') {
        if (isset($_SESSION['paciente_resultado'])) {
            $_SESSION['paciente_resultado']['observaciones'] = trim($_POST['observaciones'] ?? '');
        }
        $_SESSION['msg_observacion'] = 'Observaciones clínicas actualizadas correctamente.';
        header('Location: ../views/resultados.php');
        exit;
    }

    // Caso 2: Registro de nueva evaluación desde evaluacion.php
    $_SESSION['paciente_resultado'] = [
        'evaluacion_id' => 1,
        'tipo_paciente' => $_POST['tipo_paciente'] ?? 'nuevo',
        'rut' => !empty($_POST['paciente_rut']) ? $_POST['paciente_rut'] : '12.345.678-K',
        'nombre' => !empty($_POST['paciente_nombre']) ? $_POST['paciente_nombre'] : 'Paciente Evaluado',
        'edad' => !empty($_POST['paciente_nacimiento']) ? $_POST['paciente_nacimiento'] : 'No especificada',
        'fecha_evaluacion' => date('d/m/Y - H:i'),
        'contexto' => !empty($_POST['paciente_motivo']) ? $_POST['paciente_motivo'] : 'Atención Primaria',
        'observaciones' => trim($_POST['observaciones'] ?? '')
    ];

    header('Location: ../views/resultados.php');
    exit;
} else {
    header('Location: ../views/evaluacion.php');
    exit;
}