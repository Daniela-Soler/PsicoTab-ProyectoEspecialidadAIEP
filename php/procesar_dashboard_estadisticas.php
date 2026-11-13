<?php
session_start();
require_once 'conexion.php'; // Tu conexión segura con PDO

// 1. Validamos que solo psicólogos logueados puedan consultar esto
if (!isset($_SESSION['id_evaluador'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

// 2. Preparamos el archivo para responder exclusivamente en formato JSON
header('Content-Type: application/json; charset=utf-8');

try {
    // 3. Llamamos al Procedimiento Almacenado
    $stmt = $conn->prepare("CALL sp_obtener_estadisticas_dashboard()");
    $stmt->execute();
    
    // 4. Extraemos la fila con los totales
    $estadisticas = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si por algún motivo la BD no devuelve nada, enviamos ceros por defecto
    if (!$estadisticas) {
        $estadisticas = [
            "TotalEvaluaciones" => 0,
            "AlertasCriticas" => 0
        ];
    }
    
    // 5. Transformamos el arreglo de PHP a formato JSON y lo enviamos al frontend
    echo json_encode($estadisticas);

} catch(PDOException $e) {
    // En caso de error, también respondemos en JSON para no romper el frontend
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(["error" => "Error del servidor: " . $e->getMessage()]);
}
?>