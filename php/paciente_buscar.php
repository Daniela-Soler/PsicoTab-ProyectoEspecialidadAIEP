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

// 2. Solo permitimos método GET
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(["error" => "Método no permitido"]);
    exit();
}

// 3. Capturamos el término de búsqueda desde la URL
$busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';

if ($busqueda === '') {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Debe ingresar un RUT o nombre para buscar"]);
    exit();
}

try {
    // 4. Llamamos al procedimiento almacenado
    $sql = "CALL sp_buscar_paciente(:id_evaluador, :busqueda)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_evaluador', $_SESSION['id_evaluador'], PDO::PARAM_INT);
    $stmt->bindParam(':busqueda', $busqueda, PDO::PARAM_STR);
    $stmt->execute();

    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 5. Respondemos con la lista de coincidencias
    echo json_encode([
        "success" => true,
        "datos" => $pacientes
    ]);

} catch(PDOException $e) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(["error" => "Error al buscar paciente: " . $e->getMessage()]);
}
?>