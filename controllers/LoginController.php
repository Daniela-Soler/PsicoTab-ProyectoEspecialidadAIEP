<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Usuario y clave de prueba temporal para desarrollo
    // Usuario: admin (o admin@psicotab.cl) | Clave: 123456
    if (($usuario === 'admin' || $usuario === 'admin@psicotab.cl') && $password === '123456') {
        $_SESSION['evaluador'] = [
            'nombre' => 'Ps. Silvia Rojas',
            'rol' => 'Evaluador Clínico',
            'email' => $usuario
        ];
        // Redirige al Dashboard
        header('Location: ../views/dashboard.php');
        exit;
    } else {
        // Mensaje de error de vuelta a login.php
        $_SESSION['error_login'] = 'Credenciales inválidas. Para pruebas use: admin / 123456';
        header('Location: ../views/login.php');
        exit;
    }
} else {
    header('Location: ../views/login.php');
    exit;
}