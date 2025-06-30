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
        // nombre de equipo
        $stmt = $this->pdo->prepare("SELECT 1 FROM equipos WHERE nombre_equipo = ?");
        $stmt->execute([$data['nombre_equipo']]);
        if ($stmt->fetch()) {
            $errors[] = 'El nombre del equipo ya está en uso.';
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

            // cifrar CURP y hash de password
            $iv = str_repeat("\0", openssl_cipher_iv_length('AES-256-CBC'));
            $hashCURP = openssl_encrypt($data['curp'], 'AES-256-CBC', 'TU_LLAVE_DE_AES', OPENSSL_RAW_DATA, $iv);
            $hashPwd = password_hash($data['password1'], PASSWORD_BCRYPT);

            // insertar alumno
            $stmt = $this->pdo->prepare("
            INSERT INTO alumnos
              (boleta,nombre,apellido_paterno,apellido_materno,
               genero,curp,telefono,semestre,carrera,correo,password)
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
                $hashPwd
            ]);

            // crear equipo
            $stmt = $this->pdo->prepare("
            INSERT INTO equipos
              (nombre_equipo,nombre_proyecto,academia_id,horario_preferencia)
            VALUES (?,?,?,?)
        ");
            $stmt->execute([
                $data['nombre_equipo'],
                $data['nombre_proyecto'],
                $data['academia'],
                $data['horario']
            ]);
            $equipoId = (int) $this->pdo->lastInsertId();

            // vincular miembro
            $stmt = $this->pdo->prepare("
            INSERT INTO miembros_equipo
              (alumno_boleta,equipo_id,unidad_id)
            VALUES (?,?,?)
        ");
            $stmt->execute([
                $data['boleta'],
                $equipoId,
                $data['unidad']
            ]);

            // asignar salón
            $horarioId = $data['horario'] === 'Matutino' ? 1 : 2;
            $salon = (new AsignacionController)
                ->asignarSalon($equipoId, $horarioId);

            $this->pdo->commit();

            $_SESSION['success'] = "Registro exitoso. Salón asignado: $salon.";
            header('Location: ' . BASE_PATH . '/login/participante');
            exit;

        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            // Si aun así llega un 23000, mostramos mensaje genérico
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
              e.es_ganador,
              s.salon_id,
              hb.tipo      AS bloque,
              hb.hora_inicio,
              hb.hora_fin
            FROM alumnos a
            JOIN miembros_equipo me   ON me.alumno_boleta = a.boleta
            JOIN equipos e           ON e.id            = me.equipo_id
            LEFT JOIN asignaciones s ON s.equipo_id     = e.id
            LEFT JOIN horarios_bloques hb 
              ON hb.id = s.horario_id
            WHERE a.boleta = ?
        ");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch();

        include __DIR__ . '/../Views/participante/dashboard.php';
    }
}
