-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 14-11-2026 a las 04:01:16
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_login_evaluador` (IN `p_correo` VARCHAR(255))   BEGIN
    SELECT ID_Evaluador, Nombre, Clave 
    FROM Evaluador 
    WHERE Correo = p_correo 
    LIMIT 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_estadisticas_dashboard` ()   BEGIN
    DECLARE total_evaluaciones INT;
    DECLARE alertas_criticas INT;

    -- Contar todas las evaluaciones registradas
    SELECT COUNT(ID_Evaluacion) INTO total_evaluaciones 
    FROM EvaluacionGeneral;

    -- Contar solo los test PHQ-9 que tengan la alerta autolesiva en TRUE (1)
    SELECT COUNT(ID_PHQ9) INTO alertas_criticas 
    FROM PHQ9 
    WHERE AlertaAutolesiva = TRUE;

    -- Entregar ambos datos en un paquete limpio
    SELECT total_evaluaciones AS TotalEvaluaciones, alertas_criticas AS AlertasCriticas;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_registros_dashboard` (IN `p_busqueda` VARCHAR(255))   BEGIN
    SELECT 
        EG.ID_Evaluacion,
        P.RUT AS Identificador,
        P.Nombre AS Paciente,
        EG.Fecha_hora AS Fecha,
        -- Concatenamos inteligentemente los nombres de los tests que sí existen en la evaluación
        CONCAT_WS(', ', 
            IF(T1.ID_PHQ9 IS NOT NULL, 'PHQ-9', NULL),
            IF(T2.ID_GAD7 IS NOT NULL, 'GAD-7', NULL),
            IF(T3.ID_ASRS IS NOT NULL, 'ASRS v1.1', NULL),
            IF(T4.ID_WHODAS IS NOT NULL, 'WHODAS 2.0', NULL)
        ) AS BateriasAplicadas,
        -- Evaluamos la alerta del PHQ-9 (Si no se aplicó, mandamos 'N/A')
        IF(T1.ID_PHQ9 IS NOT NULL, 
            IF(T1.AlertaAutolesiva = 1, 'Crítica', 'Negativa'), 
        'N/A') AS AlertaPHQ9
    FROM EvaluacionGeneral EG
    INNER JOIN Paciente P ON EG.id_paciente = P.ID_Paciente
    LEFT JOIN PHQ9 T1 ON EG.ID_Evaluacion = T1.id_evaluacionP
    LEFT JOIN GAD7 T2 ON EG.ID_Evaluacion = T2.id_evaluacionG
    LEFT JOIN ASRS T3 ON EG.ID_Evaluacion = T3.id_evaluacionA
    LEFT JOIN WHODAS T4 ON EG.ID_Evaluacion = T4.id_evaluacionW
    WHERE 
        p_busqueda IS NULL 
        OR p_busqueda = '' 
        OR P.RUT LIKE CONCAT('%', p_busqueda, '%') 
        OR P.Nombre LIKE CONCAT('%', p_busqueda, '%')
    ORDER BY EG.Fecha_hora DESC;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asrs`
--

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
-- RELACIONES PARA LA TABLA `asrs`:
--   `id_evaluacionA`
--       `evaluaciongeneral` -> `ID_Evaluacion`
--

--
-- Volcado de datos para la tabla `asrs`
--

INSERT INTO `asrs` (`ID_ASRS`, `id_evaluacionA`, `item1a`, `item2a`, `item3a`, `item4a`, `item5a`, `item6a`, `AciertosUmbral`, `ResultadoTamizaje`) VALUES
(1, 3, 2, 3, 1, 0, 4, 2, 3, 'Positivo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciongeneral`
--

CREATE TABLE `evaluaciongeneral` (
  `ID_Evaluacion` int(11) NOT NULL,
  `ContextoInstitucional` enum('CESFAM','Educacional','Particular') NOT NULL,
  `id_paciente` int(11) NOT NULL,
  `id_evaluador` int(11) NOT NULL,
  `Fecha_hora` datetime DEFAULT current_timestamp(),
  `Observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `evaluaciongeneral`:
--   `id_paciente`
--       `paciente` -> `ID_Paciente`
--   `id_evaluador`
--       `evaluador` -> `ID_Evaluador`
--

--
-- Volcado de datos para la tabla `evaluaciongeneral`
--

INSERT INTO `evaluaciongeneral` (`ID_Evaluacion`, `ContextoInstitucional`, `id_paciente`, `id_evaluador`, `Fecha_hora`, `Observaciones`) VALUES
(3, 'CESFAM', 1, 1, '2026-11-12 15:01:35', 'Paciente acude a primera sesión. Refiere problemas de sueño y estrés laboral.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluador`
--

CREATE TABLE `evaluador` (
  `ID_Evaluador` int(11) NOT NULL,
  `Correo` varchar(255) NOT NULL,
  `Clave` varchar(255) NOT NULL,
  `Nombre` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `evaluador`:
--

--
-- Volcado de datos para la tabla `evaluador`
--

INSERT INTO `evaluador` (`ID_Evaluador`, `Correo`, `Clave`, `Nombre`) VALUES
(1, 'psicologo@clinica.cl', '$2y$10$3uJ6LLqAhR6Pw9bOc2j3IOQZr9thjLfBksKTqSgQW/EFKIamb5pL.', 'Dr. Roberto Navarro');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `gad7`
--

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
-- RELACIONES PARA LA TABLA `gad7`:
--   `id_evaluacionG`
--       `evaluaciongeneral` -> `ID_Evaluacion`
--

--
-- Volcado de datos para la tabla `gad7`
--

INSERT INTO `gad7` (`ID_GAD7`, `id_evaluacionG`, `item1g`, `item2g`, `item3g`, `item4g`, `item5g`, `item6g`, `item7g`, `PuntajeTotal_GAD7`, `NivelAnsiedad`) VALUES
(1, 3, 3, 2, 1, 0, 2, 1, 1, 10, 'Moderada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paciente`
--

CREATE TABLE `paciente` (
  `ID_Paciente` int(11) NOT NULL,
  `Codigo_Paciente` varchar(36) NOT NULL,
  `RUT` varchar(15) NOT NULL,
  `Nombre` varchar(150) NOT NULL,
  `Fecha_Nacimiento` date NOT NULL,
  `Sexo` enum('F','M','Otro') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELACIONES PARA LA TABLA `paciente`:
--

--
-- Volcado de datos para la tabla `paciente`
--

INSERT INTO `paciente` (`ID_Paciente`, `Codigo_Paciente`, `RUT`, `Nombre`, `Fecha_Nacimiento`, `Sexo`) VALUES
(1, '15141bf6-dec2-11f1-ae0d-5c60bae02a8a', '19876543-2', 'Rodrigo Orellana', '1995-08-15', 'F');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `phq9`
--

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
-- RELACIONES PARA LA TABLA `phq9`:
--   `id_evaluacionP`
--       `evaluaciongeneral` -> `ID_Evaluacion`
--

--
-- Volcado de datos para la tabla `phq9`
--

INSERT INTO `phq9` (`ID_PHQ9`, `id_evaluacionP`, `item1p`, `item2p`, `item3p`, `item4p`, `item5p`, `item6p`, `item7p`, `item8p`, `item9p`, `PuntajeTotal_PHQ9`, `NivelSeveridad`, `AlertaAutolesiva`) VALUES
(2, 3, 2, 1, 3, 0, 1, 1, 0, 2, 0, 10, 'Leve', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `whodas`
--

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
-- RELACIONES PARA LA TABLA `whodas`:
--   `id_evaluacionW`
--       `evaluaciongeneral` -> `ID_Evaluacion`
--

--
-- Volcado de datos para la tabla `whodas`
--

INSERT INTO `whodas` (`ID_WHODAS`, `id_evaluacionW`, `item1w`, `item2w`, `item3w`, `item4w`, `item5w`, `item6w`, `item7w`, `item8w`, `item9w`, `item10w`, `item11w`, `item12w`, `PuntajeBruto`, `PorcentajeLimitacion`, `NivelLimitacion`) VALUES
(1, 3, 1, 2, 1, 1, 3, 2, 1, 1, 2, 4, 1, 1, 20, 25.50, 'Leve');

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
  MODIFY `ID_Evaluacion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `ID_Paciente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
