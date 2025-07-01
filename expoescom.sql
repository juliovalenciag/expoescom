-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 01-07-2025 a las 20:59:56
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `expoescom`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `academias`
--

CREATE TABLE `academias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `academias`
--

INSERT INTO `academias` (`id`, `nombre`) VALUES
(1, 'Ciencia de Datos'),
(2, 'Ciencias Básicas'),
(3, 'Ciencias de la Computación'),
(4, 'Ciencias Sociales'),
(5, 'Fundamentos de Sistemas Electrónicos'),
(6, 'Ingeniería de Software'),
(7, 'Inteligencia Artificial'),
(8, 'Proyectos Estratégicos para la Toma de Decisiones'),
(9, 'Sistemas Digitales'),
(10, 'Sistemas Distribuidos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `administradores`
--

CREATE TABLE `administradores` (
  `id` int(11) NOT NULL,
  `usuario` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'Hash bcrypt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `administradores`
--

INSERT INTO `administradores` (`id`, `usuario`, `password`) VALUES
(1, 'admin', '$2y$10$7CXPq4Cp3fFacAGjRnYeqOuFWqnxXuahQiv.UpYrJUBH6py/zk3Aq');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `boleta` char(10) NOT NULL COMMENT 'Formato: PE/PP+8 dígitos o 10 dígitos',
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(50) NOT NULL,
  `apellido_materno` varchar(50) NOT NULL,
  `genero` enum('Mujer','Hombre','Otro') NOT NULL,
  `curp` varbinary(32) NOT NULL COMMENT 'CURP cifrada AES-256',
  `telefono` char(10) NOT NULL,
  `semestre` tinyint(3) UNSIGNED NOT NULL CHECK (`semestre` between 1 and 8),
  `carrera` enum('ISC','LCD','IIA') NOT NULL,
  `correo` varchar(60) NOT NULL CHECK (`correo` like '%@alumno.ipn.mx'),
  `password` varchar(255) NOT NULL COMMENT 'Hash bcrypt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`boleta`, `nombre`, `apellido_paterno`, `apellido_materno`, `genero`, `curp`, `telefono`, `semestre`, `carrera`, `correo`, `password`) VALUES
('2021392138', 'Alan Antonio', 'Gonzalez', 'Lopez', 'Hombre', 0xfb0e104050a70405a98ad0f8e89e773155050277133ba4c6e297645adaf3cee5, '5532421621', 4, 'ISC', 'alan@alumno.ipn.mx', '$2y$10$ydx54uzkjWpsyyGxj7trv.5KYnqkLkmXXJLYgt53J0P1sogVYcr8q'),
('2021435214', 'Pablo', 'Segovia', 'Torres', 'Hombre', 0xfb0e104050a70405a98ad0f8e89e77314f0825403fc9d09e572876d15d584e9f, '5512376251', 4, 'ISC', 'pablo@alumno.ipn.mx', '$2y$10$V9L6.3v7/4oYBTQ7Tx2hY.1w9oM3k71M.j.sIm6wEnL3Hl4N5jNVi'),
('2021630442', 'Julio César', 'Valencia', 'Granada', 'Hombre', 0xfb0e104050a70405a98ad0f8e89e77314f0825403fc9d09e572876d15d584e9f, '5510118810', 4, 'ISC', 'julio@alumno.ipn.mx', '$2y$10$Fi5gjMPw/ZHPlJosAig9ceV8xl/dJKb//xGO1h48OgwMBl6Qqfy.W'),
('2021634627', 'Darien', 'Castro', 'Arizmendi', 'Hombre', 0xfb0e104050a70405a98ad0f8e89e7731d802fd15beb98fdd66d17fd5661469c0, '5532713671', 4, 'ISC', 'darien@alumno.ipn.mx', '$2y$10$94MBlnS7m8wnBv2jQkB3vuSlQxNbvTaYc1MW9P0N9P8CSwRy5Sswm'),
('2021640424', 'Berenice', 'Escamilla', 'Caballero', 'Mujer', 0xfb0e104050a70405a98ad0f8e89e77314f0825403fc9d09e572876d15d584e9f, '6372169836', 5, 'ISC', 'berenice@alumno.ipn.mx', '$2y$10$OtRBUfeQqc9fst7rz/GASuVZQsRDVj5GTjfOYCmimOxKKoafWtQxG'),
('PP55555555', 'Leticia', 'Escamilla', 'Miranda', 'Mujer', 0xfb0e104050a70405a98ad0f8e89e77314f0825403fc9d09e572876d15d584e9f, '5538219382', 4, 'ISC', 'patricia@alumno.ipn.mx', '$2y$10$Gm.xfEs0YXC0OFX4l5JoLeZGd5HjAsilmVBY63s1h5yIZraFWRyQ2');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignaciones`
--

CREATE TABLE `asignaciones` (
  `id` int(11) NOT NULL,
  `equipo_id` int(11) NOT NULL,
  `salon_id` char(4) NOT NULL,
  `horario_id` tinyint(3) UNSIGNED NOT NULL,
  `fecha` date NOT NULL COMMENT 'Fecha del evento EXPOESCOM',
  `hora_inicio` time DEFAULT NULL,
  `hora_fin` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignaciones`
--

INSERT INTO `asignaciones` (`id`, `equipo_id`, `salon_id`, `horario_id`, `fecha`, `hora_inicio`, `hora_fin`) VALUES
(1, 1, '4010', 1, '2025-10-15', '10:30:00', '11:30:00'),
(2, 2, '4010', 2, '2025-10-15', '15:00:00', '16:00:00'),
(3, 3, '4010', 1, '2025-10-15', '12:00:00', '13:00:00'),
(5, 5, '4010', 2, '2025-10-15', '16:30:00', '17:30:00'),
(7, 7, '4010', 1, '2025-10-15', '13:30:00', '14:30:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `equipos`
--

CREATE TABLE `equipos` (
  `id` int(11) NOT NULL,
  `nombre_equipo` varchar(40) NOT NULL,
  `nombre_proyecto` varchar(120) NOT NULL,
  `es_ganador` tinyint(1) NOT NULL DEFAULT 0,
  `academia_id` int(11) NOT NULL,
  `horario_preferencia` enum('Matutino','Vespertino') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `equipos`
--

INSERT INTO `equipos` (`id`, `nombre_equipo`, `nombre_proyecto`, `es_ganador`, `academia_id`, `horario_preferencia`) VALUES
(1, 'Dalio', 'Kela', 0, 3, 'Matutino'),
(2, 'Alio', 'IoT', 0, 8, 'Vespertino'),
(3, 'Los yayens', 'Pugrien', 0, 3, 'Matutino'),
(5, 'Lilo', 'Stitch', 1, 8, 'Vespertino'),
(6, 'Pugs', 'Puglandia', 0, 7, 'Matutino'),
(7, 'Equipo1', 'Proyecto', 1, 9, 'Matutino'),
(8, 'Equipo2', 'Proyecto2', 0, 4, 'Matutino');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `horarios_bloques`
--

CREATE TABLE `horarios_bloques` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `tipo` enum('Matutino','Vespertino') NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `horarios_bloques`
--

INSERT INTO `horarios_bloques` (`id`, `tipo`, `hora_inicio`, `hora_fin`) VALUES
(1, 'Matutino', '10:30:00', '13:30:00'),
(2, 'Vespertino', '15:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `miembros_equipo`
--

CREATE TABLE `miembros_equipo` (
  `alumno_boleta` char(10) NOT NULL,
  `equipo_id` int(11) NOT NULL,
  `unidad_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `miembros_equipo`
--

INSERT INTO `miembros_equipo` (`alumno_boleta`, `equipo_id`, `unidad_id`) VALUES
('2021634627', 3, 18),
('2021630442', 1, 19),
('2021640424', 8, 32),
('2021435214', 6, 86),
('2021392138', 2, 100),
('PP55555555', 7, 112);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salones`
--

CREATE TABLE `salones` (
  `id` char(4) NOT NULL COMMENT 'Ej: 4011, 4010',
  `capacidad` tinyint(3) UNSIGNED NOT NULL DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `salones`
--

INSERT INTO `salones` (`id`, `capacidad`) VALUES
('4010', 5),
('4011', 5),
('4012', 5),
('4013', 5),
('4014', 5),
('4015', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_aprendizaje`
--

CREATE TABLE `unidades_aprendizaje` (
  `id` int(11) NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `academia_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidades_aprendizaje`
--

INSERT INTO `unidades_aprendizaje` (`id`, `nombre`, `academia_id`) VALUES
(1, 'Administración de Servicios en Red', 10),
(2, 'Aplicaciones para Comunicaciones en Red', 10),
(3, 'Ciberseguridad', 10),
(4, 'Computer Security', 10),
(5, 'Cómputo de Alto Desempeño', 10),
(6, 'Cómputo en la Nube', 10),
(7, 'Cómputo Paralelo', 10),
(8, 'Cryptography', 10),
(9, 'Desarrollo de Sistemas Distribuidos', 10),
(10, 'Protección de Datos', 10),
(11, 'Redes de Computadoras', 10),
(12, 'Sistemas Distribuidos', 10),
(13, 'Sistemas Operativos', 10),
(14, 'Teoría de Comunicaciones y Señales', 10),
(15, 'Algoritmos Bioinspirados', 3),
(16, 'Algoritmos y Estructuras de Datos', 3),
(17, 'Análisis y Diseño de Algoritmos', 3),
(18, 'Bioinformática Básica', 3),
(19, 'Bioinformatics', 3),
(20, 'Compiladores', 3),
(21, 'Complex Systems', 3),
(22, 'Computer Graphics', 3),
(23, 'Fundamentos de Programación', 3),
(24, 'Genetic Algorithms', 3),
(25, 'Introduction to Cryptography', 3),
(26, 'Métodos Numéricos', 3),
(27, 'Paradigmas de Programación', 3),
(28, 'Selected Topics in Cryptography', 3),
(29, 'Teoría de la Computación', 3),
(30, 'Tópicos Selectos de Algoritmos Bioinspirados', 3),
(31, 'Virtual and Augmented Reality', 3),
(32, 'Comunicación Oral y Escrita', 4),
(33, 'Desarrollo de Habilidades Sociales para la Alta Dirección', 4),
(34, 'Ética y Legalidad', 4),
(35, 'Ingeniería Ética y Sociedad', 4),
(36, 'Liderazgo Personal', 4),
(37, 'Liderazgo y Desarrollo Profesional', 4),
(38, 'Metodología de la Investigación y Divulgación Científica', 4),
(39, 'Análisis y Diseño de Sistemas', 6),
(40, 'Bases de Datos', 6),
(41, 'Desarrollo de Aplicaciones Móviles Nativas', 6),
(42, 'Desarrollo de Aplicaciones Web', 6),
(43, 'Ingeniería de Software', 6),
(44, 'Ingeniería de Software para Sistemas Inteligentes', 6),
(45, 'Non Relational Databases', 6),
(46, 'Software Quality Assurance and Design Patterns', 6),
(47, 'Tecnologías para el Desarrollo de Aplicaciones Web', 6),
(48, 'Web Application Development', 6),
(49, 'Web Client and Backend Development Frameworks', 6),
(50, 'Álgebra Lineal', 2),
(51, 'Análisis Vectorial', 2),
(52, 'Cálculo', 2),
(53, 'Cálculo Aplicado', 2),
(54, 'Cálculo Multivariable', 2),
(55, 'Economic Engineering', 2),
(56, 'Ecuaciones Diferenciales', 2),
(57, 'Estadística', 2),
(58, 'Matemáticas Avanzadas para la Ingeniería', 2),
(59, 'Matemáticas Discretas', 2),
(60, 'Mecánica y Electromagnetismo', 2),
(61, 'Probabilidad', 2),
(62, 'Probabilidad y Estadística', 2),
(63, 'Statistical Tools for Data Analytics', 2),
(64, 'Análisis de Series de Tiempo', 1),
(65, 'Analítica Avanzada de Datos', 1),
(66, 'Analítica y Visualización de Datos', 1),
(67, 'Bases de Datos Avanzadas', 1),
(68, 'Big Data', 1),
(69, 'Data Mining', 1),
(70, 'Desarrollo de Aplicaciones para Análisis de Datos', 1),
(71, 'Introducción a la Ciencia de Datos', 1),
(72, 'Matemáticas Avanzadas para la Ciencia de Datos', 1),
(73, 'Minería de Datos', 1),
(74, 'Modelado Predictivo', 1),
(75, 'Modelos Econométricos', 1),
(76, 'Procesos Estocásticos', 1),
(77, 'Programación para la Ciencia de Datos', 1),
(78, 'Circuitos Eléctricos', 5),
(79, 'Electrónica Analógica', 5),
(80, 'Instrumentación', 5),
(81, 'Instrumentación y Control', 5),
(82, 'Aplicaciones de Lenguaje Natural', 7),
(83, 'Aprendizaje de Máquina', 7),
(84, 'Aprendizaje de Máquina e Inteligencia Artificial', 7),
(85, 'Fundamentos de Inteligencia Artificial', 7),
(86, 'Image Analysis', 7),
(87, 'Inteligencia Artificial', 7),
(88, 'Interacción Humano Máquina', 7),
(89, 'Machine Learning', 7),
(90, 'Natural Language Processing', 7),
(91, 'Procesamiento de Lenguaje Natural', 7),
(92, 'Procesamiento Digital de Imágenes', 7),
(93, 'Reconocimiento de Voz', 7),
(94, 'Redes Neuronales y Aprendizaje Profundo', 7),
(95, 'Tecnologías de Lenguaje Natural', 7),
(96, 'Temas Selectos de Aprendizaje Profundo', 7),
(97, 'Temas Selectos de Inteligencia Artificial', 7),
(98, 'Visión Artificial', 7),
(99, 'Administración de Proyectos de TI', 8),
(100, 'Finanzas Empresariales', 8),
(101, 'Formulación y Evaluación de Proyectos Informáticos', 8),
(102, 'Fundamentos Económicos', 8),
(103, 'Gestión Empresarial', 8),
(104, 'High Technology Enterprise Management', 8),
(105, 'Innovación y Emprendimiento Tecnológico', 8),
(106, 'IT Governance', 8),
(107, 'Métodos Cuantitativos para la Toma de Decisiones', 8),
(108, 'Arquitectura de Computadoras', 9),
(109, 'Diseño de Sistemas Digitales', 9),
(110, 'Embedded Systems', 9),
(111, 'Fundamentos de Diseño Digital', 9),
(112, 'Internet of Things', 9),
(113, 'Introducción a los Microcontroladores', 9),
(114, 'Procesamiento de Señales', 9),
(115, 'Procesamiento Digital de Señales', 9),
(116, 'Sistemas en Chip', 9);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `academias`
--
ALTER TABLE `academias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `administradores`
--
ALTER TABLE `administradores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`boleta`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `idx_correo` (`correo`);

--
-- Indices de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipo_id` (`equipo_id`),
  ADD KEY `salon_id` (`salon_id`),
  ADD KEY `horario_id` (`horario_id`),
  ADD KEY `idx_fecha_horario` (`fecha`,`horario_id`);

--
-- Indices de la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_equipo` (`nombre_equipo`),
  ADD KEY `academia_id` (`academia_id`);

--
-- Indices de la tabla `horarios_bloques`
--
ALTER TABLE `horarios_bloques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tipo` (`tipo`);

--
-- Indices de la tabla `miembros_equipo`
--
ALTER TABLE `miembros_equipo`
  ADD PRIMARY KEY (`alumno_boleta`,`equipo_id`),
  ADD KEY `equipo_id` (`equipo_id`),
  ADD KEY `unidad_id` (`unidad_id`);

--
-- Indices de la tabla `salones`
--
ALTER TABLE `salones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `unidades_aprendizaje`
--
ALTER TABLE `unidades_aprendizaje`
  ADD PRIMARY KEY (`id`),
  ADD KEY `academia_id` (`academia_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `academias`
--
ALTER TABLE `academias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `administradores`
--
ALTER TABLE `administradores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `equipos`
--
ALTER TABLE `equipos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `unidades_aprendizaje`
--
ALTER TABLE `unidades_aprendizaje`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `asignaciones`
--
ALTER TABLE `asignaciones`
  ADD CONSTRAINT `asignaciones_ibfk_1` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `asignaciones_ibfk_2` FOREIGN KEY (`salon_id`) REFERENCES `salones` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asignaciones_ibfk_3` FOREIGN KEY (`horario_id`) REFERENCES `horarios_bloques` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `equipos`
--
ALTER TABLE `equipos`
  ADD CONSTRAINT `equipos_ibfk_1` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `miembros_equipo`
--
ALTER TABLE `miembros_equipo`
  ADD CONSTRAINT `miembros_equipo_ibfk_1` FOREIGN KEY (`alumno_boleta`) REFERENCES `alumnos` (`boleta`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `miembros_equipo_ibfk_2` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `miembros_equipo_ibfk_3` FOREIGN KEY (`unidad_id`) REFERENCES `unidades_aprendizaje` (`id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `unidades_aprendizaje`
--
ALTER TABLE `unidades_aprendizaje`
  ADD CONSTRAINT `unidades_aprendizaje_ibfk_1` FOREIGN KEY (`academia_id`) REFERENCES `academias` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
