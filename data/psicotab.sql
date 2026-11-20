-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 20-11-2026 a las 02:22:34
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `psicotab`
--
CREATE DATABASE IF NOT EXISTS `psicotab` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `psicotab`;

DELIMITER $$
--
-- Procedimientos
--
DROP PROCEDURE IF EXISTS `sp_actualizar_observaciones`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_actualizar_observaciones` (IN `p_id_evaluacion` INT, IN `p_id_evaluador` INT, IN `p_observaciones` TEXT)   BEGIN
    UPDATE EvaluacionGeneral 
    SET Observaciones = p_observaciones 
    WHERE ID_Evaluacion = p_id_evaluacion 
      AND id_evaluador = p_id_evaluador;
END$$

DROP PROCEDURE IF EXISTS `sp_buscar_paciente`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_buscar_paciente` (IN `p_id_evaluador` INT, IN `p_busqueda` VARCHAR(150))   BEGIN
    SELECT DISTINCT 
        P.ID_Paciente,
        P.RUT,
        P.Nombre,
        P.Fecha_Nacimiento,
        P.Sexo
    FROM Paciente P
    INNER JOIN EvaluacionGeneral EG ON P.ID_Paciente = EG.id_paciente
    WHERE EG.id_evaluador = p_id_evaluador
      AND (
          P.RUT LIKE CONCAT('%', p_busqueda, '%') 
          OR P.Nombre LIKE CONCAT('%', p_busqueda, '%')
      )
    LIMIT 10;
END$$

DROP PROCEDURE IF EXISTS `sp_crear_evaluacion`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_crear_evaluacion` (IN `p_contexto` ENUM('CESFAM','Educacional','Particular'), IN `p_id_paciente` INT, IN `p_id_evaluador` INT, IN `p_observaciones` TEXT)   BEGIN
    INSERT INTO EvaluacionGeneral (ContextoInstitucional, id_paciente, id_evaluador, Observaciones)
    VALUES (p_contexto, p_id_paciente, p_id_evaluador, p_observaciones);

    SELECT LAST_INSERT_ID() AS ID_Evaluacion;
END$$

DROP PROCEDURE IF EXISTS `sp_crear_paciente`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_crear_paciente` (IN `p_rut` VARCHAR(15), IN `p_nombre` VARCHAR(150), IN `p_fecha_nacimiento` DATE, IN `p_sexo` ENUM('F','M','Otro'))   BEGIN
    DECLARE v_id_paciente INT DEFAULT NULL;

    -- 1. Buscamos silenciosamente si el RUT ya existe
    SELECT ID_Paciente INTO v_id_paciente
    FROM Paciente
    WHERE RUT = p_rut
    LIMIT 1;

    -- 2. Si no existe, lo insertamos con un UUID automático
    IF v_id_paciente IS NULL THEN
        INSERT INTO Paciente (Codigo_Paciente, RUT, Nombre, Fecha_Nacimiento, Sexo)
        VALUES (UUID(), p_rut, p_nombre, p_fecha_nacimiento, p_sexo);
        
        SET v_id_paciente = LAST_INSERT_ID();
    END IF;

    -- 3. Devolvemos siempre el ID_Paciente (sea nuevo o existente)
    SELECT v_id_paciente AS ID_Paciente;
END$$

DROP PROCEDURE IF EXISTS `sp_guardar_asrs`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_asrs` (IN `p_id_evaluacion` INT, IN `p_item1` TINYINT UNSIGNED, IN `p_item2` TINYINT UNSIGNED, IN `p_item3` TINYINT UNSIGNED, IN `p_item4` TINYINT UNSIGNED, IN `p_item5` TINYINT UNSIGNED, IN `p_item6` TINYINT UNSIGNED, IN `p_aciertos_umbral` TINYINT UNSIGNED, IN `p_resultado_tamizaje` ENUM('Positivo','Negativo'))   BEGIN
    INSERT INTO ASRS (
        id_evaluacionA,
        item1a, item2a, item3a, item4a, item5a, item6a,
        AciertosUmbral, ResultadoTamizaje
    ) VALUES (
        p_id_evaluacion,
        p_item1, p_item2, p_item3, p_item4, p_item5, p_item6,
        p_aciertos_umbral, p_resultado_tamizaje
    );
END$$

DROP PROCEDURE IF EXISTS `sp_guardar_gad7`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_gad7` (IN `p_id_evaluacion` INT, IN `p_item1` TINYINT UNSIGNED, IN `p_item2` TINYINT UNSIGNED, IN `p_item3` TINYINT UNSIGNED, IN `p_item4` TINYINT UNSIGNED, IN `p_item5` TINYINT UNSIGNED, IN `p_item6` TINYINT UNSIGNED, IN `p_item7` TINYINT UNSIGNED, IN `p_puntaje_total` TINYINT UNSIGNED, IN `p_nivel_ansiedad` ENUM('Mínima','Leve','Moderada','Grave'))   BEGIN
    INSERT INTO GAD7 (
        id_evaluacionG,
        item1g, item2g, item3g, item4g, item5g, item6g, item7g,
        PuntajeTotal_GAD7, NivelAnsiedad
    ) VALUES (
        p_id_evaluacion,
        p_item1, p_item2, p_item3, p_item4, p_item5, p_item6, p_item7,
        p_puntaje_total, p_nivel_ansiedad
    );
END$$

DROP PROCEDURE IF EXISTS `sp_guardar_phq9`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_phq9` (IN `p_id_evaluacion` INT, IN `p_item1` TINYINT UNSIGNED, IN `p_item2` TINYINT UNSIGNED, IN `p_item3` TINYINT UNSIGNED, IN `p_item4` TINYINT UNSIGNED, IN `p_item5` TINYINT UNSIGNED, IN `p_item6` TINYINT UNSIGNED, IN `p_item7` TINYINT UNSIGNED, IN `p_item8` TINYINT UNSIGNED, IN `p_item9` TINYINT UNSIGNED, IN `p_puntaje_total` TINYINT UNSIGNED, IN `p_nivel_severidad` ENUM('Mínima','Leve','Moderada','Moderadamente grave','Grave'), IN `p_alerta_autolesiva` BOOLEAN)   BEGIN
    INSERT INTO PHQ9 (
        id_evaluacionP,
        item1p, item2p, item3p, item4p, item5p, item6p, item7p, item8p, item9p,
        PuntajeTotal_PHQ9, NivelSeveridad, AlertaAutolesiva
    ) VALUES (
        p_id_evaluacion,
        p_item1, p_item2, p_item3, p_item4, p_item5, p_item6, p_item7, p_item8, p_item9,
        p_puntaje_total, p_nivel_severidad, p_alerta_autolesiva
    );
END$$

DROP PROCEDURE IF EXISTS `sp_guardar_whodas`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_guardar_whodas` (IN `p_id_evaluacion` INT, IN `p_item1` TINYINT UNSIGNED, IN `p_item2` TINYINT UNSIGNED, IN `p_item3` TINYINT UNSIGNED, IN `p_item4` TINYINT UNSIGNED, IN `p_item5` TINYINT UNSIGNED, IN `p_item6` TINYINT UNSIGNED, IN `p_item7` TINYINT UNSIGNED, IN `p_item8` TINYINT UNSIGNED, IN `p_item9` TINYINT UNSIGNED, IN `p_item10` TINYINT UNSIGNED, IN `p_item11` TINYINT UNSIGNED, IN `p_item12` TINYINT UNSIGNED, IN `p_puntaje_bruto` TINYINT UNSIGNED, IN `p_porcentaje_limitacion` DECIMAL(5,2) UNSIGNED, IN `p_nivel_limitacion` ENUM('Ninguna','Leve','Moderada','Severa','Extrema'))   BEGIN
    INSERT INTO WHODAS (
        id_evaluacionW,
        item1w, item2w, item3w, item4w, item5w, item6w,
        item7w, item8w, item9w, item10w, item11w, item12w,
        PuntajeBruto, PorcentajeLimitacion, NivelLimitacion
    ) VALUES (
        p_id_evaluacion,
        p_item1, p_item2, p_item3, p_item4, p_item5, p_item6,
        p_item7, p_item8, p_item9, p_item10, p_item11, p_item12,
        p_puntaje_bruto, p_porcentaje_limitacion, p_nivel_limitacion
    );
END$$

DROP PROCEDURE IF EXISTS `sp_login_evaluador`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login_evaluador` (IN `p_correo` VARCHAR(255))   BEGIN
    SELECT ID_Evaluador, Nombre, Clave 
    FROM Evaluador 
    WHERE Correo = p_correo 
    LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS `sp_obtener_estadisticas_dashboard`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_estadisticas_dashboard` (IN `p_id_evaluador` INT)   BEGIN
    DECLARE total_evaluaciones INT;
    DECLARE alertas_criticas INT;

    -- Contar solo las evaluaciones de este evaluador
    SELECT COUNT(ID_Evaluacion) INTO total_evaluaciones 
    FROM EvaluacionGeneral
    WHERE id_evaluador = p_id_evaluador;

    -- Contar solo alertas autolesivas de evaluaciones de este evaluador
    SELECT COUNT(T1.ID_PHQ9) INTO alertas_criticas 
    FROM PHQ9 T1
    INNER JOIN EvaluacionGeneral EG ON T1.id_evaluacionP = EG.ID_Evaluacion
    WHERE T1.AlertaAutolesiva = TRUE 
      AND EG.id_evaluador = p_id_evaluador;

    SELECT total_evaluaciones AS TotalEvaluaciones, alertas_criticas AS AlertasCriticas;
END$$

DROP PROCEDURE IF EXISTS `sp_obtener_informe_completo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_informe_completo` (IN `p_id_evaluacion` INT, IN `p_id_evaluador` INT)   BEGIN
    SELECT * 
    FROM vw_informe_completo 
    WHERE ID_Evaluacion = p_id_evaluacion 
      AND ID_Evaluador = p_id_evaluador 
    LIMIT 1;
END$$

DROP PROCEDURE IF EXISTS `sp_obtener_registros_dashboard`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_registros_dashboard` (IN `p_id_evaluador` INT, IN `p_busqueda` VARCHAR(255))   BEGIN
    SELECT 
        EG.ID_Evaluacion,
        P.RUT AS Identificador,
        P.Nombre AS Paciente,
        EG.Fecha_hora AS Fecha,
        CONCAT_WS(', ', 
            IF(T1.ID_PHQ9 IS NOT NULL, 'PHQ-9', NULL),
            IF(T2.ID_GAD7 IS NOT NULL, 'GAD-7', NULL),
            IF(T3.ID_ASRS IS NOT NULL, 'ASRS v1.1', NULL),
            IF(T4.ID_WHODAS IS NOT NULL, 'WHODAS 2.0', NULL)
        ) AS BateriasAplicadas,
        IF(T1.ID_PHQ9 IS NOT NULL, 
            IF(T1.AlertaAutolesiva = 1, 'Crítica', 'Negativa'), 
        'N/A') AS AlertaPHQ9
    FROM EvaluacionGeneral EG
    INNER JOIN Paciente P ON EG.id_paciente = P.ID_Paciente
    LEFT JOIN PHQ9 T1 ON EG.ID_Evaluacion = T1.id_evaluacionP
    LEFT JOIN GAD7 T2 ON EG.ID_Evaluacion = T2.id_evaluacionG
    LEFT JOIN ASRS T3 ON EG.ID_Evaluacion = T3.id_evaluacionA
    LEFT JOIN WHODAS T4 ON EG.ID_Evaluacion = T4.id_evaluacionW
    WHERE EG.id_evaluador = p_id_evaluador
      AND (
          p_busqueda IS NULL 
          OR p_busqueda = '' 
          OR P.RUT LIKE CONCAT('%', p_busqueda, '%') 
          OR P.Nombre LIKE CONCAT('%', p_busqueda, '%')
      )
    ORDER BY EG.Fecha_hora DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asrs`
--

DROP TABLE IF EXISTS `asrs`;
CREATE TABLE `asrs` (
  `ID_ASRS` int(11) NOT NULL,
  `id_evaluacionA` int(11) NOT NULL,
  `item1a` tinyint(3) UNSIGNED NOT NULL,
  `item2a` tinyint(3) UNSIGNED NOT NULL,
  `item3a` tinyint(3) UNSIGNED NOT NULL,
  `item4a` tinyint(3) UNSIGNED NOT NULL,
  `item5a` tinyint(3) UNSIGNED NOT NULL,
  `item6a` tinyint(3) UNSIGNED NOT NULL,
  `AciertosUmbral` tinyint(3) UNSIGNED NOT NULL,
  `ResultadoTamizaje` enum('Positivo','Negativo') NOT NULL
) ;

--
-- Volcado de datos para la tabla `asrs`
--

INSERT INTO `asrs` (`ID_ASRS`, `id_evaluacionA`, `item1a`, `item2a`, `item3a`, `item4a`, `item5a`, `item6a`, `AciertosUmbral`, `ResultadoTamizaje`) VALUES
(1, 3, 2, 3, 1, 0, 4, 2, 3, 'Positivo'),
(2, 6, 2, 3, 2, 3, 1, 2, 4, 'Positivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciongeneral`
--

DROP TABLE IF EXISTS `evaluaciongeneral`;
CREATE TABLE `evaluaciongeneral` (
  `ID_Evaluacion` int(11) NOT NULL,
  `ContextoInstitucional` enum('CESFAM','Educacional','Particular') NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_evaluador` int(11) NOT NULL,
  `Fecha_hora` datetime DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluaciongeneral`
--

INSERT INTO `evaluaciongeneral` (`ID_Evaluacion`, `ContextoInstitucional`, `id_paciente`, `id_evaluador`, `Fecha_hora`, `Observaciones`) VALUES
(3, 'CESFAM', 1, 1, '2026-11-12 15:01:35', 'Paciente presenta leve mejoría en los indicadores de ansiedad. Se sugiere mantener el tratamiento actual.'),
(4, 'CESFAM', 2, 1, '2026-11-19 19:01:35', 'Paciente colaboradora durante la sesión de prueba.'),
(6, 'CESFAM', 2, 1, '2026-11-19 19:03:55', 'Prueba de batería completa.'),
(7, 'CESFAM', 2, 1, '2026-11-19 19:05:11', 'Prueba de batería completa.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluador`
--

DROP TABLE IF EXISTS `evaluador`;
CREATE TABLE `evaluador` (
  `ID_Evaluador` int(11) NOT NULL,
  `Correo` varchar(255) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluador`
--

INSERT INTO `evaluador` (`ID_Evaluador`, `Correo`, `Clave`, `Nombre`) VALUES
(1, 'psicologo@clinica.cl', '$2y$10$3uJ6LLqAhR6Pw9bOc2j3IOQZr9thjLfBksKTqSgQW/EFKIamb5pL.', 'Dr. Roberto Navarro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gad7`
--

DROP TABLE IF EXISTS `gad7`;
CREATE TABLE `gad7` (
  `ID_GAD7` int(11) NOT NULL,
  `id_evaluacionG` int(11) NOT NULL,
  `item1g` tinyint(3) UNSIGNED NOT NULL,
  `item2g` tinyint(3) UNSIGNED NOT NULL,
  `item3g` tinyint(3) UNSIGNED NOT NULL,
  `item4g` tinyint(3) UNSIGNED NOT NULL,
  `item5g` tinyint(3) UNSIGNED NOT NULL,
  `item6g` tinyint(3) UNSIGNED NOT NULL,
  `item7g` tinyint(3) UNSIGNED NOT NULL,
  `PuntajeTotal_GAD7` tinyint(3) UNSIGNED NOT NULL,
  `NivelAnsiedad` enum('Mínima','Leve','Moderada','Grave') NOT NULL
) ;

--
-- Volcado de datos para la tabla `gad7`
--

INSERT INTO `gad7` (`ID_GAD7`, `id_evaluacionG`, `item1g`, `item2g`, `item3g`, `item4g`, `item5g`, `item6g`, `item7g`, `PuntajeTotal_GAD7`, `NivelAnsiedad`) VALUES
(1, 3, 3, 2, 1, 0, 2, 1, 1, 10, 'Moderada'),
(2, 4, 2, 1, 2, 1, 0, 1, 1, 8, 'Leve'),
(3, 6, 2, 1, 2, 1, 0, 1, 1, 8, 'Leve');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

DROP TABLE IF EXISTS `paciente`;
CREATE TABLE `paciente` (
  `ID_Paciente` int(11) NOT NULL,
  `Codigo_Paciente` varchar(36) NOT NULL,
  `RUT` varchar(15) NOT NULL,
  `Nombre` varchar(150) NOT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `Sexo` enum('F','M','Otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`ID_Paciente`, `Codigo_Paciente`, `RUT`, `Nombre`, `Fecha_Nacimiento`, `Sexo`) VALUES
(1, '15141bf6-dec2-11f1-ae0d-5c60bae02a8a', '19876543-2', 'Rodrigo Orellana', '1995-08-15', 'F'),
(2, 'c95ed4b7-e464-11f1-8ebd-5c60bae02a8a', '19.876.543-2', 'Camila Soto', '1998-05-14', 'F');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `phq9`
--

DROP TABLE IF EXISTS `phq9`;
CREATE TABLE `phq9` (
  `ID_PHQ9` int(11) NOT NULL,
  `id_evaluacionP` int(11) NOT NULL,
  `item1p` tinyint(3) UNSIGNED NOT NULL,
  `item2p` tinyint(3) UNSIGNED NOT NULL,
  `item3p` tinyint(3) UNSIGNED NOT NULL,
  `item4p` tinyint(3) UNSIGNED NOT NULL,
  `item5p` tinyint(3) UNSIGNED NOT NULL,
  `item6p` tinyint(3) UNSIGNED NOT NULL,
  `item7p` tinyint(3) UNSIGNED NOT NULL,
  `item8p` tinyint(3) UNSIGNED NOT NULL,
  `item9p` tinyint(3) UNSIGNED NOT NULL,
  `PuntajeTotal_PHQ9` tinyint(3) UNSIGNED NOT NULL,
  `NivelSeveridad` enum('Mínima','Leve','Moderada','Moderadamente grave','Grave') NOT NULL,
  `AlertaAutolesiva` tinyint(1) DEFAULT 0
) ;

--
-- Volcado de datos para la tabla `phq9`
--

INSERT INTO `phq9` (`ID_PHQ9`, `id_evaluacionP`, `item1p`, `item2p`, `item3p`, `item4p`, `item5p`, `item6p`, `item7p`, `item8p`, `item9p`, `PuntajeTotal_PHQ9`, `NivelSeveridad`, `AlertaAutolesiva`) VALUES
(2, 3, 2, 1, 3, 0, 1, 1, 0, 2, 0, 10, 'Leve', 0),
(3, 4, 1, 2, 1, 2, 0, 1, 1, 0, 1, 9, 'Leve', 1),
(4, 6, 1, 2, 1, 2, 0, 1, 1, 0, 1, 9, 'Leve', 1),
(5, 7, 1, 2, 1, 2, 0, 1, 1, 0, 2, 10, 'Moderada', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vw_informe_completo`
-- (Véase abajo para la vista actual)
--
DROP VIEW IF EXISTS `vw_informe_completo`;
CREATE TABLE `vw_informe_completo` (
`ID_Evaluacion` int(11)
,`Fecha_hora` datetime
,`ContextoInstitucional` enum('CESFAM','Educacional','Particular')
,`Observaciones` text
,`ID_Evaluador` int(11)
,`Nombre_Evaluador` varchar(150)
,`ID_Paciente` int(11)
,`RUT` varchar(15)
,`Nombre_Paciente` varchar(150)
,`Fecha_Nacimiento` date
,`Sexo` enum('F','M','Otro')
,`PuntajeTotal_PHQ9` tinyint(3) unsigned
,`Nivel_PHQ9` enum('Mínima','Leve','Moderada','Moderadamente grave','Grave')
,`item9p` tinyint(3) unsigned
,`AlertaAutolesiva` tinyint(1)
,`PuntajeTotal_GAD7` tinyint(3) unsigned
,`Nivel_GAD7` enum('Mínima','Leve','Moderada','Grave')
,`Puntaje_ASRS` tinyint(3) unsigned
,`Resultado_ASRS` enum('Positivo','Negativo')
,`PuntajeBruto_WHODAS` tinyint(3) unsigned
,`Porcentaje_WHODAS` decimal(5,2) unsigned
,`Nivel_WHODAS` enum('Ninguna','Leve','Moderada','Severa','Extrema')
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `whodas`
--

DROP TABLE IF EXISTS `whodas`;
CREATE TABLE `whodas` (
  `ID_WHODAS` int(11) NOT NULL,
  `id_evaluacionW` int(11) NOT NULL,
  `item1w` tinyint(3) UNSIGNED NOT NULL,
  `item2w` tinyint(3) UNSIGNED NOT NULL,
  `item3w` tinyint(3) UNSIGNED NOT NULL,
  `item4w` tinyint(3) UNSIGNED NOT NULL,
  `item5w` tinyint(3) UNSIGNED NOT NULL,
  `item6w` tinyint(3) UNSIGNED NOT NULL,
  `item7w` tinyint(3) UNSIGNED NOT NULL,
  `item8w` tinyint(3) UNSIGNED NOT NULL,
  `item9w` tinyint(3) UNSIGNED NOT NULL,
  `item10w` tinyint(3) UNSIGNED NOT NULL,
  `item11w` tinyint(3) UNSIGNED NOT NULL,
  `item12w` tinyint(3) UNSIGNED NOT NULL,
  `PuntajeBruto` tinyint(3) UNSIGNED NOT NULL,
  `PorcentajeLimitacion` decimal(5,2) UNSIGNED NOT NULL,
  `NivelLimitacion` enum('Ninguna','Leve','Moderada','Severa','Extrema') NOT NULL
) ;

--
-- Volcado de datos para la tabla `whodas`
--

INSERT INTO `whodas` (`ID_WHODAS`, `id_evaluacionW`, `item1w`, `item2w`, `item3w`, `item4w`, `item5w`, `item6w`, `item7w`, `item8w`, `item9w`, `item10w`, `item11w`, `item12w`, `PuntajeBruto`, `PorcentajeLimitacion`, `NivelLimitacion`) VALUES
(1, 3, 1, 2, 1, 1, 3, 2, 1, 1, 2, 4, 1, 1, 20, 25.50, 'Leve'),
(2, 6, 2, 1, 2, 3, 2, 1, 2, 2, 1, 2, 1, 2, 21, 18.75, 'Leve');

-- --------------------------------------------------------

--
-- Estructura para la vista `vw_informe_completo`
--
DROP TABLE IF EXISTS `vw_informe_completo`;

DROP VIEW IF EXISTS `vw_informe_completo`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_informe_completo`  AS SELECT `eg`.`ID_Evaluacion` AS `ID_Evaluacion`, `eg`.`Fecha_hora` AS `Fecha_hora`, `eg`.`ContextoInstitucional` AS `ContextoInstitucional`, `eg`.`Observaciones` AS `Observaciones`, `ev`.`ID_Evaluador` AS `ID_Evaluador`, `ev`.`Nombre` AS `Nombre_Evaluador`, `p`.`ID_Paciente` AS `ID_Paciente`, `p`.`RUT` AS `RUT`, `p`.`Nombre` AS `Nombre_Paciente`, `p`.`Fecha_Nacimiento` AS `Fecha_Nacimiento`, `p`.`Sexo` AS `Sexo`, `phq`.`PuntajeTotal_PHQ9` AS `PuntajeTotal_PHQ9`, `phq`.`NivelSeveridad` AS `Nivel_PHQ9`, `phq`.`item9p` AS `item9p`, `phq`.`AlertaAutolesiva` AS `AlertaAutolesiva`, `gad`.`PuntajeTotal_GAD7` AS `PuntajeTotal_GAD7`, `gad`.`NivelAnsiedad` AS `Nivel_GAD7`, `asrs`.`AciertosUmbral` AS `Puntaje_ASRS`, `asrs`.`ResultadoTamizaje` AS `Resultado_ASRS`, `who`.`PuntajeBruto` AS `PuntajeBruto_WHODAS`, `who`.`PorcentajeLimitacion` AS `Porcentaje_WHODAS`, `who`.`NivelLimitacion` AS `Nivel_WHODAS` FROM ((((((`evaluaciongeneral` `eg` join `paciente` `p` on(`eg`.`id_paciente` = `p`.`ID_Paciente`)) join `evaluador` `ev` on(`eg`.`id_evaluador` = `ev`.`ID_Evaluador`)) left join `phq9` `phq` on(`eg`.`ID_Evaluacion` = `phq`.`id_evaluacionP`)) left join `gad7` `gad` on(`eg`.`ID_Evaluacion` = `gad`.`id_evaluacionG`)) left join `asrs` on(`eg`.`ID_Evaluacion` = `asrs`.`id_evaluacionA`)) left join `whodas` `who` on(`eg`.`ID_Evaluacion` = `who`.`id_evaluacionW`)) ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `asrs`
--
ALTER TABLE `asrs`
  ADD PRIMARY KEY (`ID_ASRS`),
  ADD KEY `id_evaluacionA` (`id_evaluacionA`);

--
-- Indices de la tabla `evaluaciongeneral`
--
ALTER TABLE `evaluaciongeneral`
  ADD PRIMARY KEY (`ID_Evaluacion`),
  ADD KEY `id_paciente` (`id_paciente`),
  ADD KEY `id_evaluador` (`id_evaluador`);

--
-- Indices de la tabla `evaluador`
--
ALTER TABLE `evaluador`
  ADD PRIMARY KEY (`ID_Evaluador`),
  ADD UNIQUE KEY `Correo` (`Correo`);

--
-- Indices de la tabla `gad7`
--
ALTER TABLE `gad7`
  ADD PRIMARY KEY (`ID_GAD7`),
  ADD KEY `id_evaluacionG` (`id_evaluacionG`);

--
-- Indices de la tabla `paciente`
--
ALTER TABLE `paciente`
  ADD PRIMARY KEY (`ID_Paciente`),
  ADD UNIQUE KEY `RUT` (`RUT`),
  ADD UNIQUE KEY `Codigo_Paciente` (`Codigo_Paciente`);

--
-- Indices de la tabla `phq9`
--
ALTER TABLE `phq9`
  ADD PRIMARY KEY (`ID_PHQ9`),
  ADD KEY `id_evaluacionP` (`id_evaluacionP`);

--
-- Indices de la tabla `whodas`
--
ALTER TABLE `whodas`
  ADD PRIMARY KEY (`ID_WHODAS`),
  ADD KEY `id_evaluacionW` (`id_evaluacionW`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `asrs`
--
ALTER TABLE `asrs`
  MODIFY `ID_ASRS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `evaluaciongeneral`
--
ALTER TABLE `evaluaciongeneral`
  MODIFY `ID_Evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `evaluador`
--
ALTER TABLE `evaluador`
  MODIFY `ID_Evaluador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `gad7`
--
ALTER TABLE `gad7`
  MODIFY `ID_GAD7` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `paciente`
--
ALTER TABLE `paciente`
  MODIFY `ID_Paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `phq9`
--
ALTER TABLE `phq9`
  MODIFY `ID_PHQ9` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `whodas`
--
ALTER TABLE `whodas`
  MODIFY `ID_WHODAS` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asrs`
--
ALTER TABLE `asrs`
  ADD CONSTRAINT `asrs_ibfk_1` FOREIGN KEY (`id_evaluacionA`) REFERENCES `evaluaciongeneral` (`ID_Evaluacion`);

--
-- Filtros para la tabla `evaluaciongeneral`
--
ALTER TABLE `evaluaciongeneral`
  ADD CONSTRAINT `evaluaciongeneral_ibfk_1` FOREIGN KEY (`id_paciente`) REFERENCES `paciente` (`ID_Paciente`),
  ADD CONSTRAINT `evaluaciongeneral_ibfk_2` FOREIGN KEY (`id_evaluador`) REFERENCES `evaluador` (`ID_Evaluador`);

--
-- Filtros para la tabla `gad7`
--
ALTER TABLE `gad7`
  ADD CONSTRAINT `gad7_ibfk_1` FOREIGN KEY (`id_evaluacionG`) REFERENCES `evaluaciongeneral` (`ID_Evaluacion`);

--
-- Filtros para la tabla `phq9`
--
ALTER TABLE `phq9`
  ADD CONSTRAINT `phq9_ibfk_1` FOREIGN KEY (`id_evaluacionP`) REFERENCES `evaluaciongeneral` (`ID_Evaluacion`);

--
-- Filtros para la tabla `whodas`
--
ALTER TABLE `whodas`
  ADD CONSTRAINT `whodas_ibfk_1` FOREIGN KEY (`id_evaluacionW`) REFERENCES `evaluaciongeneral` (`ID_Evaluacion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
