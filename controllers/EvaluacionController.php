<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Almacenar datos básicos del paciente en la sesión para que los lea resultados.php
    $_SESSION['paciente_resultado'] = [
        'rut' => !empty($_POST['paciente_rut']) ? $_POST['paciente_rut'] : '12.345.678-K',
        'nombre' => !empty($_POST['paciente_nombre']) ? $_POST['paciente_nombre'] : 'Paciente Evaluado',
        'edad' => !empty($_POST['paciente_nacimiento']) ? $_POST['paciente_nacimiento'] : 'No especificada',
        'fecha_evaluacion' => date('d/m/Y - H:i'),
        'contexto' => !empty($_POST['paciente_motivo']) ? $_POST['paciente_motivo'] : 'Atención Primaria'
    ];

    // Redirigir a la vista de informe
    header('Location: ../views/resultados.php');
    exit;
} else {
    header('Location: ../views/evaluacion.php');
    exit;
}