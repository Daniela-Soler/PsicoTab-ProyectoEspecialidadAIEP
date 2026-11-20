<?php 
session_start();
require_once 'conexion.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST["usuario"]) ?? '';
    $clave_ingresada = trim($_POST["password"]) ?? '';

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_login'] = "El formato del correo electrónico no es válido.";
        header("Location: ../views/login.php");
        exit();
    }

    try {
        $sql = "CALL sp_login_evaluador(:correo)";
        $stmt = $conn->prepare($sql);
        
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $evaluador = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($clave_ingresada, $evaluador['Clave'])) {
                session_regenerate_id(true);
                
                $_SESSION['id_evaluador'] = $evaluador['ID_Evaluador'];
                $_SESSION['nombre_evaluador'] = $evaluador['Nombre'];
                
                // Compatibilidad temporal con las vistas del frontend
                $_SESSION['evaluador'] = [
                    'id'     => $evaluador['ID_Evaluador'],
                    'nombre' => $evaluador['Nombre'],
                    'correo' => $correo,
                    'rol'    => 'Evaluador Clínico'
                ];

                header("Location: ../views/dashboard.php");
                exit();
                
            } 
        }
        // Mensaje genérico (buena práctica OWASP)
        $_SESSION['error_login'] = "Correo o contraseña incorrectos.";
        header("Location: ../views/login.php");
        exit();
        
    } catch(PDOException $e) {
        $_SESSION['error_login'] = "Error interno al conectar con el servidor.";
        header("Location: ../views/login.php");
        exit();
    }
} else {
    header("Location: ../views/login.php");
    exit();
}

?>