<?php
// app/Controllers/AlumnoController.php
namespace App\Controllers;

use App\Controllers\AsignacionController;

class AlumnoController
{
    protected $pdo;
    // Fecha fija de ExpoESCOM
    protected $fechaExpo = '2025-10-15';

    public function __construct()
    {
        global $pdo;  // viene de config/database.php
        $this->pdo = $pdo;
    }

    /**
     * Maneja el registro completo de un participante:
     * - Inserta alumno
     * - Crea equipo y lo vincula
     * - Asigna salón/horario automáticamente
     */
    /**
     * Maneja el registro completo de un participante:
     * - Inserta alumno
     * - Crea equipo y lo vincula
     * - Asigna salón/horario automáticamente
     */
    public function register()
    {
        // 1. Capturar y sanear
        $data = [
            'boleta' => trim($_POST['boleta'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
            'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
            'genero' => $_POST['genero'] ?? '',
            'curp' => strtoupper(trim($_POST['curp'] ?? '')),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'semestre' => (int) ($_POST['semestre'] ?? 0),
            'carrera' => $_POST['carrera'] ?? '',
            'correo' => trim($_POST['correo'] ?? ''),
            'password1' => $_POST['password1'] ?? '',
            'password2' => $_POST['password2'] ?? '',
            'academia' => (int) ($_POST['academia'] ?? 0),
            'unidad' => (int) ($_POST['unidad'] ?? 0),
            'horario' => $_POST['horario'] ?? '',
            'nombre_equipo' => trim($_POST['nombre_equipo'] ?? ''),
            'nombre_proyecto' => trim($_POST['nombre_proyecto'] ?? '')
        ];

        $errors = [];

        // 2. VALIDACIONES PREVIAS A LA TRANSACCIÓN
        // 2.1 Formato y obligatorios (igual que antes)...
        if (!preg_match('/^(?:\d{10}|(?:PE|PP)\d{8})$/', $data['boleta'])) {
            $errors[] = 'Boleta inválida. Debe ser 10 dígitos o PE/PP + 8 dígitos.';
        }
        foreach (['nombre', 'apellido_paterno', 'apellido_materno'] as $f) {
            if ($data[$f] === '') {
                $errors[] = ucfirst(str_replace('_', ' ', $f)) . ' es obligatorio.';
            }
        }
        if (!in_array($data['genero'], ['Mujer', 'Hombre', 'Otro'])) {
            $errors[] = 'Género no válido.';
        }
        if (!preg_match('/^[A-ZÑ]{4}\d{6}[HM][A-ZÑ]{5}[A-Z0-9]\d$/', $data['curp'])) {
            $errors[] = 'CURP inválida (18 mayúsculas).';
        }
        if (!preg_match('/^\d{10}$/', $data['telefono'])) {
            $errors[] = 'Teléfono debe tener 10 dígitos.';
        }
        if ($data['semestre'] < 1 || $data['semestre'] > 8) {
            $errors[] = 'Semestre inválido.';
        }
        if (!in_array($data['carrera'], ['ISC', 'LCD', 'IIA'])) {
            $errors[] = 'Carrera no válida.';
        }
        if (
            !filter_var($data['correo'], FILTER_VALIDATE_EMAIL) ||
            !str_ends_with($data['correo'], '@alumno.ipn.mx')
        ) {
            $errors[] = 'Correo institucional inválido.';
        }
        if (
            strlen($data['password1']) < 8 ||
            !preg_match('/[A-Z]/', $data['password1']) ||
            !preg_match('/\d/', $data['password1']) ||
            !preg_match('/\W/', $data['password1'])
        ) {
            $errors[] = 'Contraseña debe tener 8+ car., 1 may., 1 díg. y 1 especial.';
        }
        if ($data['password1'] !== $data['password2']) {
            $errors[] = 'Las contraseñas no coinciden.';
        }
        if ($data['nombre_equipo'] === '' || $data['nombre_proyecto'] === '') {
            $errors[] = 'Nombre de equipo y proyecto son obligatorios.';
        }
        // 2.2 Validar existencia de academia/unidad
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM academias WHERE id = ?");
        $stmt->execute([$data['academia']]);
        if ($stmt->fetchColumn() == 0) {
            $errors[] = 'Academia no válida.';
        }
        $stmt = $this->pdo->prepare("
        SELECT COUNT(*) 
          FROM unidades_aprendizaje 
         WHERE id = ? AND academia_id = ?
    ");
        $stmt->execute([$data['unidad'], $data['academia']]);
        if ($stmt->fetchColumn() == 0) {
            $errors[] = 'Unidad no válida para la academia seleccionada.';
        }
        if (!in_array($data['horario'], ['Matutino', 'Vespertino'])) {
            $errors[] = 'Horario preferido no válido.';
        }

        // 2.3 COMPROBAR unicidad directamente
        // boleta
        $stmt = $this->pdo->prepare("SELECT 1 FROM alumnos WHERE boleta = ?");
        $stmt->execute([$data['boleta']]);
        if ($stmt->fetch()) {
            $errors[] = 'Ya existe un alumno con esa boleta.';
        }
        // correo
        $stmt = $this->pdo->prepare("SELECT 1 FROM alumnos WHERE correo = ?");
        $stmt->execute([$data['correo']]);
        if ($stmt->fetch()) {
            $errors[] = 'El correo ya está registrado.';
        }
        // Si hay **cualquiera** errores, vuelvo a la forma
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: ' . BASE_PATH . '/register');
            exit;
        }

        // 3. TRANSACCIÓN: insertar todo
        try {
            $this->pdo->beginTransaction();

            // 3.1. Cifrar CURP y generar hash de contraseña
            $iv = str_repeat("\0", openssl_cipher_iv_length('AES-256-CBC'));
            $hashCURP = openssl_encrypt(
                $data['curp'],
                'AES-256-CBC',
                'TU_LLAVE_DE_AES',
                OPENSSL_RAW_DATA,
                $iv
            );
            $hashPwd = password_hash($data['password1'], PASSWORD_BCRYPT);

            // 3.2. Insertar alumno
            $stmt = $this->pdo->prepare("
        INSERT INTO alumnos
          (boleta, nombre, apellido_paterno, apellido_materno,
           genero, curp, telefono, semestre, carrera, correo, password)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)
    ");
            $stmt->execute([
                $data['boleta'],
                $data['nombre'],
                $data['apellido_paterno'],
                $data['apellido_materno'],
                $data['genero'],
                $hashCURP,
                $data['telefono'],
                $data['semestre'],
                $data['carrera'],
                $data['correo'],
                $hashPwd,
            ]);

            // 3.3. Buscar o crear equipo y asociar al alumno

            // — 1) Intentar obtener el equipo existente
            $stmt = $this->pdo->prepare("
        SELECT id
          FROM equipos
         WHERE nombre_equipo = ?
         LIMIT 1
    ");
            $stmt->execute([$data['nombre_equipo']]);
            $equipoId = $stmt->fetchColumn();

            // — 2) Si no existe, crearlo
            if (!$equipoId) {
                $stmt = $this->pdo->prepare("
            INSERT INTO equipos
              (nombre_equipo, nombre_proyecto, academia_id, horario_preferencia)
            VALUES (?,?,?,?)
        ");
                $stmt->execute([
                    $data['nombre_equipo'],
                    $data['nombre_proyecto'],
                    $data['academia'],
                    $data['horario'],
                ]);
                $equipoId = (int) $this->pdo->lastInsertId();
            }

            // — 3) Asociar al alumno (o actualizar unidad si ya existía)
            $stmt = $this->pdo->prepare("
        INSERT INTO miembros_equipo
          (alumno_boleta, equipo_id, unidad_id)
        VALUES (?,?,?)
        ON DUPLICATE KEY
          UPDATE unidad_id = VALUES(unidad_id)
    ");
            $stmt->execute([
                $data['boleta'],
                $equipoId,
                $data['unidad'],
            ]);

            // 3.4. Asignar salón/horario automáticamente
            try {
                $horarioId = $data['horario'] === 'Matutino' ? 1 : 2;
                $salon = (new AsignacionController)
                    ->asignarSalon($equipoId, $horarioId);
            } catch (\Exception $e) {
                // si no hay salones disponibles
                $this->pdo->rollBack();
                $errors[] = $e->getMessage();  // “No hay salones disponibles…”
                $_SESSION['errors'] = $errors;
                $_SESSION['old'] = $data;
                header('Location: ' . BASE_PATH . '/register');
                exit;
            }

            $this->pdo->commit();

            $boleta = $data['boleta'];
            include __DIR__ . '/../Views/auth/register-success.php';
            exit;

        } catch (\PDOException $e) {
            $this->pdo->rollBack();

            // 3.6. Manejo de errores
            if ($e->getCode() === '23000') {
                $errors[] = 'Ya existe un registro duplicado en la base de datos.';
            } else {
                $errors[] = 'Error inesperado: ' . $e->getMessage();
            }
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;
            header('Location: ' . BASE_PATH . '/register');
            exit;
        }
    }

    /**
     * Muestra el dashboard del participante con descarga de Acuse/Diploma.
     */
    public function dashboard()
    {
        $boleta = $_SESSION['alumno_boleta'] ?? null;
        if (!$boleta) {
            header('Location: ' . BASE_PATH . '/login/participante');
            exit;
        }

        $stmt = $this->pdo->prepare("
SELECT
      a.boleta,
      a.nombre,
      a.apellido_paterno,
      a.apellido_materno,
      a.genero,
      a.telefono,
      a.correo,
      a.semestre,
      a.carrera,
      e.nombre_equipo,
      e.nombre_proyecto,
      e.es_ganador,
      s.salon_id,
      s.hora_inicio,
      s.hora_fin,
      hb.tipo AS bloque
    FROM alumnos a
    JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
    JOIN equipos e ON e.id = me.equipo_id
    LEFT JOIN asignaciones s ON s.equipo_id = e.id
    LEFT JOIN horarios_bloques hb ON hb.id = s.horario_id
    WHERE a.boleta = ?
");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch(\PDO::FETCH_ASSOC); // <-- importante para acceder con claves string

        include __DIR__ . '/../Views/participante/dashboard.php';
    }

    /**
     * Actualiza el perfil del participante.
     */
    public function updateProfile()
    {
        $boleta = $_SESSION['alumno_boleta'] ?? null;
        if (!$boleta) {
            header('Location:' . BASE_PATH . '/login/participante');
            exit;
        }

        // 1) Captura y sanea todo
        $fields = [
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'genero',
            'telefono',
            'correo',
            'current_password',
            'new_password',
            'confirm_password'
        ];
        foreach ($fields as $f) {
            $$f = trim($_POST[$f] ?? '');
        }

        $errors = [];

        // Validaciones básicas
        if ($nombre === '' || $apellido_paterno === '' || $apellido_materno === '') {
            $errors[] = 'Nombre y apellidos son obligatorios.';
        }
        if (!in_array($genero, ['Mujer', 'Hombre', 'Otro'])) {
            $errors[] = 'Género inválido.';
        }
        if (!preg_match('/^\d{10}$/', $telefono)) {
            $errors[] = 'Teléfono inválido.';
        }
        if (
            !filter_var($correo, FILTER_VALIDATE_EMAIL) ||
            !str_ends_with($correo, '@alumno.ipn.mx')
        ) {
            $errors[] = 'Correo inválido.';
        }
        // unicidad correo
        $stmt = $this->pdo->prepare("SELECT 1 FROM alumnos WHERE correo = ? AND boleta <> ?");
        $stmt->execute([$correo, $boleta]);
        if ($stmt->fetch()) {
            $errors[] = 'Correo ya en uso.';
        }

        // Si cambian contraseña
        if ($new_password !== '' || $confirm_password !== '' || $current_password !== '') {
            // 1) trae hash actual
            $stmt = $this->pdo->prepare("SELECT password FROM alumnos WHERE boleta = ?");
            $stmt->execute([$boleta]);
            $hash = $stmt->fetchColumn();
            if (!password_verify($current_password, $hash)) {
                $errors[] = 'Contraseña actual incorrecta.';
            }
            if (
                $new_password === '' || strlen($new_password) < 8 ||
                !preg_match('/[A-Z]/', $new_password) ||
                !preg_match('/\d/', $new_password) ||
                !preg_match('/\W/', $new_password)
            ) {
                $errors[] = 'La nueva contraseña no cumple requisitos.';
            }
            if ($new_password !== $confirm_password) {
                $errors[] = 'Las contraseñas nuevas no coinciden.';
            }
            $newHash = password_hash($new_password, PASSWORD_BCRYPT);
        }

        if ($errors) {
            $_SESSION['errors_profile'] = $errors;
            header('Location:' . BASE_PATH . '/participante');
            exit;
        }

        // 2) Update transaccional
        $this->pdo->beginTransaction();
        // datos básicos
        $stmt = $this->pdo->prepare("
      UPDATE alumnos
         SET nombre=?, apellido_paterno=?, apellido_materno=?,
             genero=?, telefono=?, correo=?
       WHERE boleta=?
    ");
        $stmt->execute([
            $nombre,
            $apellido_paterno,
            $apellido_materno,
            $genero,
            $telefono,
            $correo,
            $boleta
        ]);
        if (isset($newHash)) {
            $stmt = $this->pdo->prepare("UPDATE alumnos SET password = ? WHERE boleta = ?");
            $stmt->execute([$newHash, $boleta]);
        }
        $this->pdo->commit();

        $_SESSION['success_profile'] = 'Perfil actualizado correctamente.';
        header('Location:' . BASE_PATH . '/participante');
        exit;
    }



}
