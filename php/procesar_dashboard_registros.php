<?php
session_start();
require_once 'conexion.php';

// Seguridad: Solo evaluadores autenticados
if (!isset($_SESSION['id_evaluador'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

// Capturamos el texto de búsqueda si existe (por método GET)
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

try {
    // Preparamos la llamada al Procedimiento Almacenado
    $stmt = $conn->prepare("CALL sp_obtener_registros_dashboard(:busqueda)");
    
    // Vinculamos el parámetro de búsqueda de forma segura
    $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
    $stmt->execute();
    
    // Extraemos todos los registros encontrados
    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Devolvemos el array completo en formato JSON
    echo json_encode($registros);

} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(["error" => "Error del servidor: " . $e->getMessage()]);
}
?>