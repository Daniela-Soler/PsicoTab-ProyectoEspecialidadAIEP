<?php 
session_start();
require_once 'conexion.php'; // Llamamos a tu archivo de conexión PDO

// Verificamos que la información viaje por método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Limpiamos espacios en blanco de los extremos
    $correo = trim($_POST["usuario"]);
    $clave_ingresada = trim($_POST["password"]);

    // 1. Validación de seguridad básica: formato de correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        die("Error: Formato de correo institucional inválido.");
    }

    try {
        // 2. Consulta Preparada (Seguridad contra Inyección SQL)
        // Seleccionamos solo lo estrictamente necesario
        $sql = "CALL sp_login_evaluador(:correo)";
        $stmt = $conn->prepare($sql);
        
        // Vinculamos la variable de forma segura
        $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
        $stmt->execute();

        // 3. Verificamos si el correo existe en la base de datos
        if ($stmt->rowCount() == 1) {
            
            // Obtenemos los datos del evaluador
            $evaluador = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 4. Verificamos la contraseña
            // password_verify compara el texto plano con el hash guardado en la BD
            if (password_verify($clave_ingresada, $evaluador['Clave'])) {
                
                // ¡Login exitoso! Creamos las variables de sesión
                $_SESSION['id_evaluador'] = $evaluador['ID_Evaluador'];
                $_SESSION['nombre_evaluador'] = $evaluador['Nombre'];
                
                // Respuesta temporal para que sepas que funcionó
                echo "Login exitoso. Bienvenido/a, Ps. " . $evaluador['Nombre'];
                
                // Cuando unas todo con el frontend, reemplazarás el 'echo' de arriba por:
                // header("Location: dashboard.php");
                // exit();
                
            } else {
                echo "Error: La contraseña es incorrecta.";
            }
        } else {
            echo "Error: El correo ingresado no está registrado.";
        }
        
    } catch(PDOException $e) {
        // En producción no debes mostrar el error real de la BD al usuario, 
        // pero para tu desarrollo es útil verlo.
        echo "Error en el sistema: " . $e->getMessage();
    }
} else {
    echo "Acceso no autorizado.";
}


// Patrón regex que permite letras minúsculas, matúsculas, acentos, ñ y espacios.
//$patron = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/";

//ctype_alpha($texto): Devuelve true solo si todo son letras (sin espacios).
//ctype_digit($texto): Devuelve true solo si todo son números.
//ctype_alnum($texto): Devuelve true solo si son letras y números.

//$correo = $_POST['correo'];

//if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
//    echo "Error: Formato de correo no válido.";
//}

//Contra Inyecciones de Frontend (XSS): Para campos de texto libre 
// (como tu campo de Observaciones donde el psicólogo sí debe poder escribir comas, puntos y símbolos libremente), 
// no validas los caracteres al entrar, sino que los neutralizas antes de imprimirlos en pantalla usando htmlspecialchars($texto). 
// Esto convierte cualquier intento de inyectar <script>malicioso</script> en texto inofensivo

?>