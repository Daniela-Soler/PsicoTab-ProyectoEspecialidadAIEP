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

// 3. Leemos el paquete JSON enviado desde el frontend
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['paciente']) || !isset($input['evaluacion']) || !isset($input['tests'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Estructura incompleta. Se requiere paciente, evaluacion y tests."]);
    exit();
}

$paciente   = $input['paciente'];
$evaluacion = $input['evaluacion'];
$tests      = $input['tests'];

// Validamos que venga al menos un test
if (empty($tests['phq9']) && empty($tests['gad7']) && empty($tests['asrs']) && empty($tests['whodas'])) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(["error" => "Debe enviar al menos un test completado."]);
    exit();
}

try {
    // INICIO DE LA TRANSACCIÓN 
    $conn->beginTransaction();

    // PASO 1: Resolver ID_Paciente (Antiguo o Nuevo con Find-or-Create)
    if (!empty($paciente['id_paciente']) && is_numeric($paciente['id_paciente'])) {
        $id_paciente = (int)$paciente['id_paciente'];
    } else {
        $stmt = $conn->prepare("CALL sp_crear_paciente(:rut, :nombre, :fecha_nac, :sexo)");
        $stmt->bindParam(':rut',       $paciente['rut'],              PDO::PARAM_STR);
        $stmt->bindParam(':nombre',    $paciente['nombre'],           PDO::PARAM_STR);
        $stmt->bindParam(':fecha_nac', $paciente['fecha_nacimiento'], PDO::PARAM_STR);
        $stmt->bindParam(':sexo',      $paciente['sexo'],             PDO::PARAM_STR);
        $stmt->execute();

        $resPaciente = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor(); 

        $id_paciente = (int)$resPaciente['ID_Paciente'];
    }

    // PASO 2: Crear Evaluación General
    $observaciones = isset($evaluacion['observaciones']) ? trim($evaluacion['observaciones']) : '';

    $stmt = $conn->prepare("CALL sp_crear_evaluacion(:contexto, :id_paciente, :id_evaluador, :observaciones)");
    $stmt->bindParam(':contexto',      $evaluacion['contexto'],      PDO::PARAM_STR);
    $stmt->bindParam(':id_paciente',   $id_paciente,                 PDO::PARAM_INT);
    $stmt->bindParam(':id_evaluador',  $_SESSION['id_evaluador'],    PDO::PARAM_INT);
    $stmt->bindParam(':observaciones', $observaciones,               PDO::PARAM_STR);
    $stmt->execute();

    $resEval = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    $id_evaluacion = (int)$resEval['ID_Evaluacion'];

    // PASO 3: Guardar PHQ-9 (si fue aplicado)
    if (!empty($tests['phq9']) && count($tests['phq9']) === 9) {
        $items = array_map('intval', $tests['phq9']);
        $total = array_sum($items);

        if ($total <= 4)      $nivel = 'Mínima';
        elseif ($total <= 9)  $nivel = 'Leve';
        elseif ($total <= 14) $nivel = 'Moderada';
        elseif ($total <= 19) $nivel = 'Moderadamente grave';
        else                  $nivel = 'Grave';

        // Alerta autolesiva si el ítem 9 (índice 8) es >= 1
        $alerta = ($items[8] >= 1) ? 1 : 0;

        $stmt = $conn->prepare("CALL sp_guardar_phq9(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_evaluacion,
            $items[0], $items[1], $items[2], $items[3], $items[4],
            $items[5], $items[6], $items[7], $items[8],
            $total, $nivel, $alerta
        ]);
        $stmt->closeCursor();
    }

    // PASO 4: Guardar GAD-7 (si fue aplicado)
    if (!empty($tests['gad7']) && count($tests['gad7']) === 7) {
        $items = array_map('intval', $tests['gad7']);
        $total = array_sum($items);

        if ($total <= 4)      $nivel = 'Mínima';
        elseif ($total <= 9)  $nivel = 'Leve';
        elseif ($total <= 14) $nivel = 'Moderada';
        else                  $nivel = 'Grave';

        $stmt = $conn->prepare("CALL sp_guardar_gad7(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_evaluacion,
            $items[0], $items[1], $items[2], $items[3], $items[4], $items[5], $items[6],
            $total, $nivel
        ]);
        $stmt->closeCursor();
    }

    // PASO 5: Guardar ASRS v1.1 (si fue aplicado)
    if (!empty($tests['asrs']) && count($tests['asrs']) === 6) {
        $items = array_map('intval', $tests['asrs']);

        // Umbrales oficiales Parte A: ítems 1-3 (>=2), ítems 4-6 (>=3)
        $aciertos = 0;
        if ($items[0] >= 2) $aciertos++;
        if ($items[1] >= 2) $aciertos++;
        if ($items[2] >= 2) $aciertos++;
        if ($items[3] >= 3) $aciertos++;
        if ($items[4] >= 3) $aciertos++;
        if ($items[5] >= 3) $aciertos++;

        $resultado = ($aciertos >= 4) ? 'Positivo' : 'Negativo';

        $stmt = $conn->prepare("CALL sp_guardar_asrs(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_evaluacion,
            $items[0], $items[1], $items[2], $items[3], $items[4], $items[5],
            $aciertos, $resultado
        ]);
        $stmt->closeCursor();
    }

    // PASO 6: Guardar WHODAS 2.0 (si fue aplicado)
    if (!empty($tests['whodas']) && count($tests['whodas']) === 12) {
        $items = array_map('intval', $tests['whodas']);
        $bruto = array_sum($items); // Rango: 12 a 60

        // Conversión a porcentaje (0% a 100%)
        $porcentaje = round((($bruto - 12) / 48) * 100, 2);

        // Baremo estándar CIF (OMS)
        if ($porcentaje <= 4)       $nivel = 'Ninguna';
        elseif ($porcentaje <= 24)  $nivel = 'Leve';
        elseif ($porcentaje <= 49)  $nivel = 'Moderada';
        elseif ($porcentaje <= 95)  $nivel = 'Severa';
        else                        $nivel = 'Extrema';

        $stmt = $conn->prepare("CALL sp_guardar_whodas(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_evaluacion,
            $items[0], $items[1], $items[2], $items[3], $items[4], $items[5],
            $items[6], $items[7], $items[8], $items[9], $items[10], $items[11],
            $bruto, $porcentaje, $nivel
        ]);
        $stmt->closeCursor();
    }

    // CONFIRMACIÓN DE LA TRANSACCIÓN
    $conn->commit();

    echo json_encode([
        "success"       => true,
        "mensaje"       => "Evaluación guardada correctamente",
        "id_evaluacion" => $id_evaluacion
    ]);

} catch (PDOException $e) {
    // Si cualquier SP falla (ej. un CHECK fuera de rango), deshacemos todo
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(["error" => "Error al guardar la evaluación: " . $e->getMessage()]);
}
?>