<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// 1. Vaciamos todas las variables de sesión
$_SESSION = [];

// 2. Eliminamos la cookie de sesión del navegador (si existe)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Destruimos la sesión en el servidor
session_destroy();

echo json_encode([
    "success" => true,
    "mensaje" => "Sesión cerrada correctamente"
]);
?>