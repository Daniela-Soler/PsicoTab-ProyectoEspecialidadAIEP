<?php 
    $host = "localhost";
    $usuario = "root";
    $clave = "";
    $base_datos = "psicotab";

    try {
        // Se establece la conexión PDO con soporte para caracteres UTF-8
        $conn = new PDO("mysql:host=$host;dbname=$base_datos;charset=utf8mb4", $usuario, $clave);

        // Configurar PDO para que lance excepciones en caso de errores
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    } catch(PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
?>