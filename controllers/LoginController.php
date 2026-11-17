<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Validación en servidor del formato de correo electrónico
    if (!filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_login'] = 'El formato del correo electrónico no es válido.';
        header('Location: ../views/login.php');
        exit;
    }

    // Credenciales simuladas temporales (actualizadas a formato correo)
    if ($usuario === 'admin@psicotab.cl' && $password === '123456') {
        $_SESSION['evaluador'] = [
            'id' => 1,
            'nombre' => 'Ps. Silvia Rojas',
            'correo' => $usuario,
            'rol' => 'Evaluador Clínico'
        ];
        header('Location: ../views/dashboard.php');
        exit;
    } else {
        $_SESSION['error_login'] = 'Correo o contraseña incorrectos.';
        header('Location: ../views/login.php');
        exit;
    }
} else {
    header('Location: ../views/login.php');
    exit;
}