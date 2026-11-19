<?php
session_start();
require_once 'conexion.php';

// 1. Barrera de seguridad
if (!isset($_SESSION['id_evaluador'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(["error" => "Acceso no autorizado"]);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

// 2. Solo permitimos método GET para lectura
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

// 3. Capturamos y validamos el ID de la evaluación desde la URL
$id_evaluacion = isset($_GET['id_evaluacion']) ? $_GET['id_evaluacion'] : null;

if (!$id_evaluacion || !is_numeric($id_evaluacion)) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Falta un ID de evaluación válido"]);
    exit();
}

try {
    // 4. Consultamos la vista filtrando también por el evaluador logueado (anti-IDOR)
    $sql = "CALL sp_obtener_informe_completo(:id_evaluacion, :id_evaluador)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_evaluacion', $id_evaluacion, PDO::PARAM_INT);
    $stmt->bindParam(':id_evaluador', $_SESSION['id_evaluador'], PDO::PARAM_INT);
    $stmt->execute();

    $informe = $stmt->fetch(PDO::FETCH_ASSOC);

    // 5. Verificamos si existe (o si pertenece a este evaluador)
    if (!$informe) {
        header('HTTP/1.1 404 Not Found');
        echo json_encode(["error" => "Informe no encontrado o sin permisos de acceso"]);
        exit();
    }

    // 6. Devolvemos el paquete completo en JSON
    echo json_encode([
        "success" => true,
        "datos" => $informe
    ]);

} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(["error" => "Error al consultar el informe: " . $e->getMessage()]);
}
?>