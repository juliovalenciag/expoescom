-- ====================================================
-- 1. Creación de la base de datos
-- ====================================================
CREATE DATABASE IF NOT EXISTS expoescom
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE expoescom;

-- ====================================================
-- 2. Catálogos fijos
-- ====================================================

-- 2.1. Academias
CREATE TABLE academias (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 2.2. Unidades de aprendizaje
CREATE TABLE unidades_aprendizaje (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(80) NOT NULL,
  academia_id INT NOT NULL,
  FOREIGN KEY (academia_id)
    REFERENCES academias(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 2.3. Salones
CREATE TABLE salones (
  id        CHAR(2) PRIMARY KEY COMMENT 'Ej: A1, B2',
  capacidad TINYINT UNSIGNED NOT NULL DEFAULT 5
) ENGINE=InnoDB;

-- 2.4. Bloques de horario
CREATE TABLE horarios_bloques (
  id          TINYINT UNSIGNED PRIMARY KEY,
  tipo        ENUM('Matutino','Vespertino') NOT NULL UNIQUE,
  hora_inicio TIME NOT NULL,
  hora_fin    TIME NOT NULL
) ENGINE=InnoDB;

-- ====================================================
-- 3. Datos de usuarios y equipos
-- ====================================================

-- 3.1. Alumnos (participantes)
CREATE TABLE alumnos (
  boleta   CHAR(10) PRIMARY KEY COMMENT 'Formato: PE/PP+8 dígitos o 10 dígitos',
  nombre   VARCHAR(100) NOT NULL,
  apellido_paterno VARCHAR(50) NOT NULL,
  apellido_materno VARCHAR(50) NOT NULL,
  genero   ENUM('Mujer','Hombre','Otro') NOT NULL,
  curp     VARBINARY(32)   NOT NULL COMMENT 'CURP cifrada AES-256',
  telefono CHAR(10)        NOT NULL,
  semestre TINYINT UNSIGNED NOT NULL
    CHECK (semestre BETWEEN 1 AND 8),
  carrera  ENUM('ISC','LCD','IIA') NOT NULL,
  correo   VARCHAR(60) NOT NULL UNIQUE
    CHECK (correo LIKE '%@alumno.ipn.mx'),
  password VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt',
  INDEX idx_correo (correo)
) ENGINE=InnoDB;

-- 3.2. Equipos de proyecto
CREATE TABLE equipos (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  nombre_equipo       VARCHAR(40)  NOT NULL UNIQUE,
  nombre_proyecto     VARCHAR(120) NOT NULL,
  es_ganador          BOOLEAN      NOT NULL DEFAULT FALSE,
  academia_id         INT          NOT NULL,
  horario_preferencia ENUM('Matutino','Vespertino') NOT NULL,
  FOREIGN KEY (academia_id)
    REFERENCES academias(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- 3.3. Miembros de equipo (N:M alumnos–equipos)
CREATE TABLE miembros_equipo (
  alumno_boleta CHAR(10) NOT NULL,
  equipo_id     INT       NOT NULL,
  unidad_id     INT       NOT NULL,
  PRIMARY KEY (alumno_boleta, equipo_id),
  FOREIGN KEY (alumno_boleta)
    REFERENCES alumnos(boleta)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (equipo_id)
    REFERENCES equipos(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (unidad_id)
    REFERENCES unidades_aprendizaje(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE
) ENGINE=InnoDB;

DELIMITER $$
CREATE PROCEDURE sp_registrar_en_equipo (
  IN p_boleta        CHAR(10),
  IN p_nombre_equipo VARCHAR(40),
  IN p_nombre_proy   VARCHAR(120),
  IN p_academia_id   INT,
  IN p_horario_pref  ENUM('Matutino','Vespertino'),
  IN p_unidad_id     INT
)
BEGIN
  DECLARE v_equipo_id INT;

  -- 1. Intentar obtener el equipo existente
  SELECT id
    INTO v_equipo_id
    FROM equipos
   WHERE nombre_equipo = p_nombre_equipo
   LIMIT 1;

  -- 2. Si no existe, crear uno nuevo
  IF v_equipo_id IS NULL THEN
    INSERT INTO equipos (
      nombre_equipo,
      nombre_proyecto,
      academia_id,
      horario_preferencia
    ) VALUES (
      p_nombre_equipo,
      p_nombre_proy,
      p_academia_id,
      p_horario_pref
    );
    SET v_equipo_id = LAST_INSERT_ID();
  END IF;

  -- 3. Asociar al alumno al equipo
  INSERT INTO miembros_equipo (
    alumno_boleta,
    equipo_id,
    unidad_id
  ) VALUES (
    p_boleta,
    v_equipo_id,
    p_unidad_id
  )
  ON DUPLICATE KEY UPDATE
    unidad_id = VALUES(unidad_id);
END$$
DELIMITER ;

-- ====================================================
-- 4. Asignaciones de salón, fecha y horario
-- ====================================================
CREATE TABLE asignaciones (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  equipo_id  INT NOT NULL UNIQUE,
  salon_id   CHAR(2) NOT NULL,
  horario_id TINYINT UNSIGNED NOT NULL,
  fecha      DATE NOT NULL COMMENT 'Fecha del evento EXPOESCOM',
  FOREIGN KEY (equipo_id)
    REFERENCES equipos(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (salon_id)
    REFERENCES salones(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  FOREIGN KEY (horario_id)
    REFERENCES horarios_bloques(id)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  INDEX idx_fecha_horario (fecha, horario_id)
) ENGINE=InnoDB;

-- Trigger para controlar capacidad por salón/bloque
DELIMITER $$
CREATE TRIGGER trg_before_insert_asignacion
BEFORE INSERT ON asignaciones
FOR EACH ROW
BEGIN
  DECLARE cnt INT; DECLARE cap INT;
  SELECT COUNT(*) INTO cnt
    FROM asignaciones
   WHERE salon_id = NEW.salon_id
     AND horario_id = NEW.horario_id
     AND fecha = NEW.fecha;
  SELECT capacidad INTO cap
    FROM salones
   WHERE id = NEW.salon_id;
  IF cnt >= cap THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Capacidad excedida en salón/horario';
  END IF;
END$$
DELIMITER ;

-- ====================================================
-- 5. Administrador único
-- ====================================================
CREATE TABLE administradores (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  usuario  VARCHAR(30)  NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL COMMENT 'Hash bcrypt'
) ENGINE=InnoDB;

-- ====================================================
-- 6. Datos iniciales
-- ====================================================

-- 6.1. Academias
INSERT INTO academias (nombre) VALUES
  ('Ciencia de Datos'),
  ('Ciencias Básicas'),
  ('Ciencias de la Computación'),
  ('Ciencias Sociales'),
  ('Fundamentos de Sistemas Electrónicos'),
  ('Ingeniería de Software'),
  ('Inteligencia Artificial'),
  ('Proyectos Estratégicos para la Toma de Decisiones'),
  ('Sistemas Digitales'),
  ('Sistemas Distribuidos');

-- 6.2. Unidades por academia
INSERT INTO unidades_aprendizaje (nombre, academia_id) VALUES
  -- Sistemas Distribuidos
  ('Administración de Servicios en Red',       (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Aplicaciones para Comunicaciones en Red',  (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Ciberseguridad',                           (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Computer Security',                        (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Cómputo de Alto Desempeño',                (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Cómputo en la Nube',                       (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Cómputo Paralelo',                         (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Cryptography',                             (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Desarrollo de Sistemas Distribuidos',      (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Protección de Datos',                      (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Redes de Computadoras',                    (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Sistemas Distribuidos',                    (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Sistemas Operativos',                      (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),
  ('Teoría de Comunicaciones y Señales',       (SELECT id FROM academias WHERE nombre='Sistemas Distribuidos')),

  -- Ciencias de la Computación
  ('Algoritmos Bioinspirados',                 (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Algoritmos y Estructuras de Datos',        (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Análisis y Diseño de Algoritmos',          (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Bioinformática Básica',                    (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Bioinformatics',                           (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Compiladores',                             (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Complex Systems',                          (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Computer Graphics',                        (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Fundamentos de Programación',              (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Genetic Algorithms',                       (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Introduction to Cryptography',             (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Métodos Numéricos',                        (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Paradigmas de Programación',               (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Selected Topics in Cryptography',          (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Teoría de la Computación',                 (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Tópicos Selectos de Algoritmos Bioinspirados',(SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),
  ('Virtual and Augmented Reality',            (SELECT id FROM academias WHERE nombre='Ciencias de la Computación')),

  -- Ciencias Sociales
  ('Comunicación Oral y Escrita',              (SELECT id FROM academias WHERE nombre='Ciencias Sociales')),
  ('Desarrollo de Habilidades Sociales para la Alta Dirección',(SELECT id FROM academias WHERE nombre='Ciencias Sociales')),
  ('Ética y Legalidad',                        (SELECT id FROM academias WHERE nombre='Ciencias Sociales')),
  ('Ingeniería Ética y Sociedad',              (SELECT id FROM academias WHERE nombre='Ciencias Sociales')),
  ('Liderazgo Personal',                       (SELECT id FROM academias WHERE nombre='Ciencias Sociales')),
  ('Liderazgo y Desarrollo Profesional',       (SELECT id FROM academias WHERE nombre='Ciencias Sociales')),
  ('Metodología de la Investigación y Divulgación Científica',(SELECT id FROM academias WHERE nombre='Ciencias Sociales')),

  -- Ingeniería de Software
  ('Análisis y Diseño de Sistemas',            (SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Bases de Datos',                           (SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Desarrollo de Aplicaciones Móviles Nativas',(SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Desarrollo de Aplicaciones Web',           (SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Ingeniería de Software',                   (SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Ingeniería de Software para Sistemas Inteligentes',(SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Non Relational Databases',                 (SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Software Quality Assurance and Design Patterns',(SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Tecnologías para el Desarrollo de Aplicaciones Web',(SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Web Application Development',              (SELECT id FROM academias WHERE nombre='Ingeniería de Software')),
  ('Web Client and Backend Development Frameworks',(SELECT id FROM academias WHERE nombre='Ingeniería de Software')),

  -- Ciencias Básicas
  ('Álgebra Lineal',                           (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Análisis Vectorial',                       (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Cálculo',                                  (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Cálculo Aplicado',                         (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Cálculo Multivariable',                    (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Economic Engineering',                     (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Ecuaciones Diferenciales',                 (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Estadística',                              (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Matemáticas Avanzadas para la Ingeniería', (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Matemáticas Discretas',                    (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Mecánica y Electromagnetismo',             (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Probabilidad',                             (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Probabilidad y Estadística',               (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),
  ('Statistical Tools for Data Analytics',     (SELECT id FROM academias WHERE nombre='Ciencias Básicas')),

  -- Ciencia de Datos
  ('Análisis de Series de Tiempo',             (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Analítica Avanzada de Datos',              (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Analítica y Visualización de Datos',       (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Bases de Datos Avanzadas',                 (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Big Data',                                 (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Data Mining',                              (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Desarrollo de Aplicaciones para Análisis de Datos',(SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Introducción a la Ciencia de Datos',       (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Matemáticas Avanzadas para la Ciencia de Datos',(SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Minería de Datos',                         (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Modelado Predictivo',                      (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Modelos Econométricos',                    (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Procesos Estocásticos',                    (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),
  ('Programación para la Ciencia de Datos',    (SELECT id FROM academias WHERE nombre='Ciencia de Datos')),

  -- Fundamentos de Sistemas Electrónicos
  ('Circuitos Eléctricos',                     (SELECT id FROM academias WHERE nombre='Fundamentos de Sistemas Electrónicos')),
  ('Electrónica Analógica',                    (SELECT id FROM academias WHERE nombre='Fundamentos de Sistemas Electrónicos')),
  ('Instrumentación',                          (SELECT id FROM academias WHERE nombre='Fundamentos de Sistemas Electrónicos')),
  ('Instrumentación y Control',                (SELECT id FROM academias WHERE nombre='Fundamentos de Sistemas Electrónicos')),

  -- Inteligencia Artificial
  ('Aplicaciones de Lenguaje Natural',         (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Aprendizaje de Máquina',                   (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Aprendizaje de Máquina e Inteligencia Artificial',(SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Fundamentos de Inteligencia Artificial',   (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Image Analysis',                           (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Inteligencia Artificial',                  (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Interacción Humano Máquina',               (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Machine Learning',                         (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Natural Language Processing',              (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Procesamiento de Lenguaje Natural',        (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Procesamiento Digital de Imágenes',        (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Reconocimiento de Voz',                    (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Redes Neuronales y Aprendizaje Profundo',  (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Tecnologías de Lenguaje Natural',          (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Temas Selectos de Aprendizaje Profundo',   (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Temas Selectos de Inteligencia Artificial',(SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),
  ('Visión Artificial',                        (SELECT id FROM academias WHERE nombre='Inteligencia Artificial')),

  -- Proyectos Estratégicos para la Toma de Decisiones
  ('Administración de Proyectos de TI',        (SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('Finanzas Empresariales',                   (SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('Formulación y Evaluación de Proyectos Informáticos',(SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('Fundamentos Económicos',                   (SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('Gestión Empresarial',                      (SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('High Technology Enterprise Management',    (SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('Innovación y Emprendimiento Tecnológico', (SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('IT Governance',                            (SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),
  ('Métodos Cuantitativos para la Toma de Decisiones',(SELECT id FROM academias WHERE nombre='Proyectos Estratégicos para la Toma de Decisiones')),

  -- Sistemas Digitales
  ('Arquitectura de Computadoras',             (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Diseño de Sistemas Digitales',             (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Embedded Systems',                         (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Fundamentos de Diseño Digital',            (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Internet of Things',                       (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Introducción a los Microcontroladores',    (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Procesamiento de Señales',                 (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Procesamiento Digital de Señales',         (SELECT id FROM academias WHERE nombre='Sistemas Digitales')),
  ('Sistemas en Chip',                         (SELECT id FROM academias WHERE nombre='Sistemas Digitales'));

-- 6.3. Salones
INSERT INTO salones (id, capacidad) VALUES
  ('4010',5),('4011',5),('4012',5),('4013',5),('4014',5);

-- 6.4. Bloques horarios
INSERT INTO horarios_bloques (id, tipo, hora_inicio, hora_fin) VALUES
  (1,'Matutino','10:30:00','13:30:00'),
  (2,'Vespertino','15:00:00','18:00:00');

INSERT INTO administradores (usuario, password) VALUES
  ('admin', '$2y$10$7CXPq4Cp3fFacAGjRnYeqOuFWqnxXuahQiv.UpYrJUBH6py/zk3Aq');

ALTER TABLE asignaciones
  ADD COLUMN hora_inicio TIME AFTER fecha,
  ADD COLUMN hora_fin TIME AFTER hora_inicio;
