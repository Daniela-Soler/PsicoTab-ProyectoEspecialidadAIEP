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

// 2. Solo permitimos método POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

// 3. Capturamos los datos enviados desde el frontend 
$id_evaluacion = isset($_POST['id_evaluacion']) ? $_POST['id_evaluacion'] : null;
$observaciones = isset($_POST['observaciones']) ? trim($_POST['observaciones']) : '';

// 4. Validamos que al menos venga el ID
if (!$id_evaluacion) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Falta el ID de la evaluación"]);
    exit();
}

try {
    // 5. Llamamos el procedimiento almacenado
    $sql = "CALL sp_actualizar_observaciones(:id_evaluacion, :id_evaluador, :observaciones)";
    $stmt = $conn->prepare($sql);
    
    // Vinculamos las variables
    $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
    $stmt->bindParam(':id_evaluacion', $id_evaluacion, PDO::PARAM_INT);
    $stmt->bindParam(':id_evaluador', $_SESSION['id_evaluador'], PDO::PARAM_INT);
    
    // Ejecutamos la consulta
    $stmt->execute();

    // 6. Respondemos con éxito
    echo json_encode([
        "success" => true, 
        "mensaje" => "Observaciones guardadas correctamente"
    ]);

} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(["error" => "Error al actualizar en BD: " . $e->getMessage()]);
}
?>