<?php
namespace App\Controllers;

use App\Controllers\AsignacionController;

class AdminController
{
    /** @var \PDO */
    protected $pdo;

    public function __construct()
    {
        // La sesión y la conexión PDO ya están inicializadas en routes.php
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * 6) Dashboard: totales de alumnos, equipos y ganadores
     */
    public function dashboard()
    {
        // 1) Totales
        $totales = [
            'alumnos' => (int) $this->pdo->query("SELECT COUNT(*) FROM alumnos")->fetchColumn(),
            'equipos' => (int) $this->pdo->query("SELECT COUNT(*) FROM equipos")->fetchColumn(),
            'ganadores' => (int) $this->pdo->query("SELECT COUNT(*) FROM equipos WHERE es_ganador = 1")->fetchColumn(),
            'salones' => (int) $this->pdo->query("SELECT COUNT(*) FROM salones")->fetchColumn(),
            'bloques' => (int) $this->pdo->query("SELECT COUNT(*) FROM horarios_bloques")->fetchColumn(),
        ];

        include __DIR__ . '/../Views/admin/dashboard.php';
        exit;
    }
    /**
     * 7) Vista HTML: listado de participantes
     */

    public function list()
    {
        $totales = [
            'alumnos' => (int) $this->pdo->query("SELECT COUNT(*) FROM alumnos")->fetchColumn(),
            'equipos' => (int) $this->pdo->query("SELECT COUNT(*) FROM equipos")->fetchColumn(),
            'ganadores' => (int) $this->pdo->query("SELECT COUNT(*) FROM equipos WHERE es_ganador = 1")->fetchColumn(),
        ];
        include __DIR__ . '/../Views/admin/participantes.php';
    }

    /**
     * GET /admin/participantes
     */
    public function participantes()
    {
        $stmt = $this->pdo->query("
        SELECT
          a.boleta,
          a.nombre,
          a.apellido_paterno,
          a.apellido_materno,
          a.correo,
          a.telefono,
          a.genero,
          a.semestre,
          a.carrera,
          e.nombre_equipo,
          e.nombre_proyecto,
          ac.nombre AS academia,
          ua.nombre AS unidad,
          e.es_ganador
        FROM alumnos a
        JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
        JOIN equipos e        ON e.id            = me.equipo_id
        JOIN academias ac     ON ac.id           = e.academia_id
        JOIN unidades_aprendizaje ua ON ua.id     = me.unidad_id
        ORDER BY a.boleta
    ");
        $participantes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../Views/admin/participantes.php';
    }

    /**
     * GET  /admin/api/participantes
     * POST /admin/api/participantes
     * PUT  /admin/api/participantes/{boleta}
     * DELETE /admin/api/participantes/{boleta}
     * POST /admin/api/participantes/{boleta}/ganador
     */
    public function apiList()
    {
        header('Content-Type: application/json');
        $this->pdo
            ->query("SELECT boleta,nombre,apellido_paterno,apellido_materno,correo,telefono,semestre,carrera FROM alumnos")
            ->execute();
        echo json_encode($this->pdo->query("SELECT boleta,nombre,apellido_paterno,apellido_materno,correo,telefono,semestre,carrera FROM alumnos")->fetchAll());
    }

    public function apiCreate()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        // 1) Validaciones mínimas (puedes extender)
        $errors = [];
        if (!preg_match('/^(?:\d{10}|(?:PE|PP)\d{8})$/', $data['boleta'] ?? '')) {
            $errors[] = 'Boleta inválida';
        }
        if (!preg_match('/^[A-ZÑ]{4}\d{6}[HM][A-ZÑ]{5}[A-Z0-9]\d$/', $data['curp'] ?? '')) {
            $errors[] = 'CURP inválida';
        }
        if (
            !filter_var($data['correo'], FILTER_VALIDATE_EMAIL)
            || !str_ends_with($data['correo'], '@alumno.ipn.mx')
        ) {
            $errors[] = 'Correo institucional inválido';
        }
        if ($errors) {
            http_response_code(422);
            echo json_encode(['errors' => $errors]);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // 2) Encriptar CURP y hashear password
            $iv = str_repeat("\0", openssl_cipher_iv_length('AES-256-CBC'));
            $hashCURP = openssl_encrypt(
                strtoupper($data['curp']),
                'AES-256-CBC',
                'TU_LLAVE_DE_AES',
                OPENSSL_RAW_DATA,
                $iv
            );
            $hashPwd = password_hash($data['password'], PASSWORD_BCRYPT);

            // 3) Insertar alumno
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

            // 4) Buscar o crear equipo
            $stmt = $this->pdo->prepare("
                SELECT id
                  FROM equipos
                 WHERE nombre_equipo = ?
                 LIMIT 1
            ");
            $stmt->execute([$data['nombre_equipo']]);
            $equipoId = $stmt->fetchColumn();

            if (!$equipoId) {
                $stmt = $this->pdo->prepare("
                    INSERT INTO equipos
                      (nombre_equipo,nombre_proyecto,academia_id,horario_preferencia)
                    VALUES (?,?,?,?)
                ");
                $stmt->execute([
                    $data['nombre_equipo'],
                    $data['nombre_proyecto'],
                    $data['academia_id'],
                    $data['horario_preferencia']
                ]);
                $equipoId = (int) $this->pdo->lastInsertId();
            }

            // 5) Asociar al alumno con unidad
            $stmt = $this->pdo->prepare("
                INSERT INTO miembros_equipo
                  (alumno_boleta,equipo_id,unidad_id)
                VALUES (?,?,?)
            ");
            $stmt->execute([
                $data['boleta'],
                $equipoId,
                $data['unidad_id']
            ]);

            $this->pdo->commit();
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiUpdate(string $boleta)
    {
        $data = json_decode(file_get_contents("php://input"), true);
        try {
            if (!empty($data['password'])) {
                $stmt = $this->pdo->prepare("
                    UPDATE alumnos SET
                      nombre=?,apellido_paterno=?,apellido_materno=?,
                      genero=?,telefono=?,semestre=?,carrera=?,correo=?,password=?
                    WHERE boleta=?
                ");
                $stmt->execute([
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'],
                    $data['genero'],
                    $data['telefono'],
                    $data['semestre'],
                    $data['carrera'],
                    $data['correo'],
                    password_hash($data['password'], PASSWORD_BCRYPT),
                    $boleta
                ]);
            } else {
                $stmt = $this->pdo->prepare("
                    UPDATE alumnos SET
                      nombre=?,apellido_paterno=?,apellido_materno=?,
                      genero=?,telefono=?,semestre=?,carrera=?,correo=?
                    WHERE boleta=?
                ");
                $stmt->execute([
                    $data['nombre'],
                    $data['apellido_paterno'],
                    $data['apellido_materno'],
                    $data['genero'],
                    $data['telefono'],
                    $data['semestre'],
                    $data['carrera'],
                    $data['correo'],
                    $boleta
                ]);
            }
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiDelete(string $boleta)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM alumnos WHERE boleta=?");
            $stmt->execute([$boleta]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiToggleWinner(string $boleta)
    {
        // alterna es_ganador del equipo al que pertenece el alumno
        $stmt = $this->pdo->prepare("
            SELECT equipo_id FROM miembros_equipo WHERE alumno_boleta=?
        ");
        $stmt->execute([$boleta]);
        $equipoId = $stmt->fetchColumn();
        if (!$equipoId) {
            http_response_code(404);
            echo json_encode(['error' => 'Equipo no encontrado']);
            return;
        }
        try {
            $this->pdo
                ->prepare("UPDATE equipos SET es_ganador = NOT es_ganador WHERE id=?")
                ->execute([$equipoId]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function showAddParticipant()
    {
        // cargar academias
        $academias = $this->pdo
            ->query("SELECT id, nombre FROM academias ORDER BY nombre")
            ->fetchAll(\PDO::FETCH_ASSOC);

        // cargar unidades y agruparlas por academia
        $stmt = $this->pdo->query("SELECT id, nombre, academia_id FROM unidades_aprendizaje ORDER BY nombre");
        $unidades = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $unidadesPorAcademia = [];
        foreach ($unidades as $u) {
            $unidadesPorAcademia[$u['academia_id']][] = [
                'id' => $u['id'],
                'nombre' => $u['nombre'],
            ];
        }

        include __DIR__ . '/../Views/admin/participantes_add.php';
        exit;
    }

    /**
     * POST /admin/participantes/add
     */
    public function storeParticipant()
    {
        // 1) Recoger y sanear
        $data = [
            'boleta' => trim($_POST['boleta'] ?? ''),
            'nombre' => trim($_POST['nombre'] ?? ''),
            'apellido_paterno' => trim($_POST['apellido_paterno'] ?? ''),
            'apellido_materno' => trim($_POST['apellido_materno'] ?? ''),
            'genero' => $_POST['genero'] ?? '',
            'curp' => strtoupper(trim($_POST['curp'] ?? '')),
            'telefono' => trim($_POST['telefono'] ?? ''),
            'correo_local' => trim($_POST['correo_local'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'semestre' => (int) ($_POST['semestre'] ?? 0),
            'carrera' => $_POST['carrera'] ?? '',
            'academia_id' => (int) ($_POST['academia_id'] ?? 0),
            'unidad_id' => (int) ($_POST['unidad_id'] ?? 0),
            'horario_preferencia' => $_POST['horario_preferencia'] ?? '',
            'nombre_equipo' => trim($_POST['nombre_equipo'] ?? ''),
            'nombre_proyecto' => trim($_POST['nombre_proyecto'] ?? ''),
        ];

        // 2) Validaciones
        $errors = [];
        if (!preg_match('/^(?:\d{10}|(?:PE|PP)\d{8})$/', $data['boleta'])) {
            $errors[] = 'Boleta inválida.';
        }
        if (!in_array($data['genero'], ['Mujer', 'Hombre', 'Otro'], true)) {
            $errors[] = 'Género inválido.';
        }
        if (!preg_match('/^[A-ZÑ][AEIOUÑ][A-ZÑ]{2}\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])[HM][A-ZÑ]{5}[A-Z0-9]\d$/', $data['curp'])) {
            $errors[] = 'CURP inválida.';
        }
        if (!preg_match('/^\d{10}$/', $data['telefono'])) {
            $errors[] = 'Teléfono inválido.';
        }
        if (!preg_match('/^[\w.+-]+$/', $data['correo_local'])) {
            $errors[] = 'Correo local inválido.';
        }
        if (strlen($data['password']) < 8) {
            $errors[] = 'Contraseña muy corta.';
        }
        if ($data['semestre'] < 1 || $data['semestre'] > 8) {
            $errors[] = 'Semestre inválido.';
        }
        if (!in_array($data['carrera'], ['ISC', 'LCD', 'IIA'], true)) {
            $errors[] = 'Carrera inválida.';
        }
        // academia + unidad
        $stmt = $this->pdo->prepare("SELECT 1 FROM academias WHERE id = ?");
        $stmt->execute([$data['academia_id']]);
        if (!$stmt->fetchColumn()) {
            $errors[] = 'Academia inválida.';
        }
        $stmt = $this->pdo->prepare("SELECT 1 FROM unidades_aprendizaje WHERE id = ? AND academia_id = ?");
        $stmt->execute([$data['unidad_id'], $data['academia_id']]);
        if (!$stmt->fetchColumn()) {
            $errors[] = 'Unidad inválida para esa academia.';
        }
        if ($data['nombre_equipo'] === '' || $data['nombre_proyecto'] === '') {
            $errors[] = 'Equipo y proyecto son obligatorios.';
        }

        if ($errors) {
            $_SESSION['errors_add'] = $errors;
            header('Location: ' . BASE_PATH . '/admin/participantes/add');
            exit;
        }

        // 3) Insertar dentro de una transacción
        try {
            $this->pdo->beginTransaction();
            // 3.1 Alumnos
            $iv = str_repeat("\0", openssl_cipher_iv_length('AES-256-CBC'));
            $encCURP = openssl_encrypt($data['curp'], 'AES-256-CBC', 'TU_LLAVE_DE_AES', OPENSSL_RAW_DATA, $iv);
            $hashPwd = password_hash($data['password'], PASSWORD_BCRYPT);

            $stmt = $this->pdo->prepare("
          INSERT INTO alumnos
            (boleta,nombre,apellido_paterno,apellido_materno,
             genero,curp,telefono,semestre,carrera,correo,password)
          VALUES (?,?,?,?,?,?,?,?,?,CONCAT(?,'@alumno.ipn.mx'),?)
        ");
            $stmt->execute([
                $data['boleta'],
                $data['nombre'],
                $data['apellido_paterno'],
                $data['apellido_materno'],
                $data['genero'],
                $encCURP,
                $data['telefono'],
                $data['semestre'],
                $data['carrera'],
                $data['correo_local'],
                $hashPwd
            ]);

            // 3.2 Equipos (buscar o crear)
            $stmt = $this->pdo->prepare("SELECT id FROM equipos WHERE nombre_equipo = ?");
            $stmt->execute([$data['nombre_equipo']]);
            $equipoId = $stmt->fetchColumn();
            if (!$equipoId) {
                $stmt = $this->pdo->prepare("
              INSERT INTO equipos (nombre_equipo,nombre_proyecto,academia_id,horario_preferencia)
              VALUES (?,?,?,?)
            ");
                $stmt->execute([
                    $data['nombre_equipo'],
                    $data['nombre_proyecto'],
                    $data['academia_id'],
                    $data['horario_preferencia']
                ]);
                $equipoId = $this->pdo->lastInsertId();
            }

            // 3.3 Miembros
            $stmt = $this->pdo->prepare("
          INSERT INTO miembros_equipo (alumno_boleta,equipo_id,unidad_id)
          VALUES (?,?,?)
        ");
            $stmt->execute([
                $data['boleta'],
                $equipoId,
                $data['unidad_id']
            ]);

            $this->pdo->commit();
            $_SESSION['success_part'] = 'Participante creado correctamente.';
            header('Location: ' . BASE_PATH . '/admin/participantes');
            exit;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $_SESSION['errors_add'] = ['Error al guardar: ' . $e->getMessage()];
            header('Location: ' . BASE_PATH . '/admin/participantes/add');
            exit;
        }
    }

    public function showEditParticipant(string $boleta)
    {
        // 1) traer datos del participante junto con su equipo, academia y unidad
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
              me.unidad_id,
              e.id   AS equipo_id,
              e.nombre_equipo,
              e.nombre_proyecto,
              e.academia_id
            FROM alumnos a
            JOIN miembros_equipo me   ON me.alumno_boleta = a.boleta
            JOIN equipos e            ON e.id            = me.equipo_id
            WHERE a.boleta = ?
            LIMIT 1
        ");
        $stmt->execute([$boleta]);
        $participant = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$participant) {
            header('HTTP/1.0 404 Not Found');
            echo "Participante no encontrado.";
            exit;
        }

        // academias (igual que en add)
        $academias = $this->pdo
            ->query("SELECT id, nombre FROM academias ORDER BY nombre")
            ->fetchAll(\PDO::FETCH_ASSOC);

        // unidades agrupadas
        $stmt = $this->pdo->query("SELECT id, nombre, academia_id FROM unidades_aprendizaje ORDER BY nombre");
        $unidades = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $unidadesPorAcademia = [];
        foreach ($unidades as $u) {
            $unidadesPorAcademia[$u['academia_id']][] = [
                'id' => $u['id'],
                'nombre' => $u['nombre'],
            ];
        }

        include __DIR__ . '/../Views/admin/participantes_edit.php';
        exit;
    }

    public function updateParticipant(string $boleta)
    {
        // 1) Validar existencia
        $stmt = $this->pdo->prepare("SELECT 1 FROM alumnos WHERE boleta = ?");
        $stmt->execute([$boleta]);
        if (!$stmt->fetchColumn()) {
            header('HTTP/1.0 404 Not Found');
            echo "Participante no encontrado.";
            exit;
        }

        // 2) Capturar y sanear
        $nombre = trim($_POST['nombre'] ?? '');
        $apellidoP = trim($_POST['apellido_paterno'] ?? '');
        $apellidoM = trim($_POST['apellido_materno'] ?? '');
        $genero = $_POST['genero'] ?? '';
        $telefono = trim($_POST['telefono'] ?? '');
        $correoLocal = trim($_POST['correo_local'] ?? '');
        $semestre = (int) ($_POST['semestre'] ?? 0);
        $carrera = $_POST['carrera'] ?? '';
        $academiaId = (int) ($_POST['academia_id'] ?? 0);
        $unidadId = (int) ($_POST['unidad_id'] ?? 0);
        $nombreEquipo = trim($_POST['nombre_equipo'] ?? '');
        $nombreProyecto = trim($_POST['nombre_proyecto'] ?? '');

        $errors = [];
        // (aquí puedes agregar validaciones como patrón de boleta, teléfono, correo_local, etc.)

        if ($nombre === '' || $apellidoP === '' || $apellidoM === '') {
            $errors[] = 'Nombre y apellidos son obligatorios.';
        }
        if (!in_array($genero, ['Mujer', 'Hombre', 'Otro'], true)) {
            $errors[] = 'Género inválido.';
        }
        if (!preg_match('/^\d{10}$/', $telefono)) {
            $errors[] = 'Teléfono inválido.';
        }
        if ($semestre < 1 || $semestre > 8) {
            $errors[] = 'Semestre inválido.';
        }
        if (!in_array($carrera, ['ISC', 'LCD', 'IIA'], true)) {
            $errors[] = 'Carrera inválida.';
        }
        // correo_local
        if (!preg_match('/^[\w.+-]+$/', $correoLocal)) {
            $errors[] = 'Correo local inválido.';
        }
        // academia + unidad
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM academias WHERE id = ?");
        $stmt->execute([$academiaId]);
        if ($stmt->fetchColumn() == 0) {
            $errors[] = 'Academia inválida.';
        }
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM unidades_aprendizaje WHERE id = ? AND academia_id = ?");
        $stmt->execute([$unidadId, $academiaId]);
        if ($stmt->fetchColumn() == 0) {
            $errors[] = 'Unidad inválida para la academia.';
        }
        if ($nombreEquipo === '' || $nombreProyecto === '') {
            $errors[] = 'Equipo y proyecto son obligatorios.';
        }

        if ($errors) {
            $_SESSION['errors_part'] = $errors;
            header("Location: " . BASE_PATH . "/admin/participantes/edit/{$boleta}");
            exit;
        }

        // 3) Actualizar en transacción
        try {
            $this->pdo->beginTransaction();

            // 3.1. Update alumnos
            $stmt = $this->pdo->prepare("
                UPDATE alumnos SET
                  nombre=?, apellido_paterno=?, apellido_materno=?,
                  genero=?, telefono=?, semestre=?, carrera=?, correo=CONCAT(?, '@alumno.ipn.mx')
                WHERE boleta=?
            ");
            $stmt->execute([
                $nombre,
                $apellidoP,
                $apellidoM,
                $genero,
                $telefono,
                $semestre,
                $carrera,
                $correoLocal,
                $boleta
            ]);

            // 3.2. Update equipos
            // primero obtener equipo_id
            $stmt = $this->pdo->prepare("
                SELECT equipo_id FROM miembros_equipo WHERE alumno_boleta=?
            ");
            $stmt->execute([$boleta]);
            $equipoId = (int) $stmt->fetchColumn();

            $stmt = $this->pdo->prepare("
                UPDATE equipos
                   SET nombre_equipo=?, nombre_proyecto=?, academia_id=?
                 WHERE id = ?
            ");
            $stmt->execute([$nombreEquipo, $nombreProyecto, $academiaId, $equipoId]);

            // 3.3. Update unidad en miembros_equipo
            $stmt = $this->pdo->prepare("
                UPDATE miembros_equipo
                   SET unidad_id=?
                 WHERE alumno_boleta=? AND equipo_id=?
            ");
            $stmt->execute([$unidadId, $boleta, $equipoId]);

            $this->pdo->commit();
            $_SESSION['success_part'] = 'Participante actualizado.';
            header("Location: " . BASE_PATH . "/admin/participantes");
            exit;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            $_SESSION['errors_part'] = ['Error al actualizar: ' . $e->getMessage()];
            header("Location: " . BASE_PATH . "/admin/participantes/edit/{$boleta}");
            exit;
        }
    }


    public function salones()
    {
        // 1) Obtengo para cada salón su capacidad y la ocupación por bloque
        $sql = "
      SELECT
        s.id         AS salon,
        s.capacidad,
        COALESCE(SUM(a.horario_id = 1), 0) AS ocup_mat,
        COALESCE(SUM(a.horario_id = 2), 0) AS ocup_vesp
      FROM salones s
      LEFT JOIN asignaciones a
        ON a.salon_id = s.id
      GROUP BY s.id
      ORDER BY s.id
    ";
        $stmt = $this->pdo->query($sql);
        $salones = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 2) Paso al view
        include __DIR__ . '/../Views/admin/salones.php';
    }
    public function salonesIndex()
    {
        $stmt = $this->pdo->prepare("
          SELECT s.id AS salon, s.capacidad,
                 COALESCE(SUM(CASE WHEN hb.tipo='Matutino' THEN 1 END),0) AS ocup_mat,
                 COALESCE(SUM(CASE WHEN hb.tipo='Vespertino' THEN 1 END),0) AS ocup_vesp
          FROM salones s
          LEFT JOIN asignaciones a
            ON a.salon_id = s.id AND a.fecha = ?
          LEFT JOIN horarios_bloques hb
            ON hb.id = a.horario_id
          GROUP BY s.id, s.capacidad
          ORDER BY s.id
        ");
        $stmt->execute([$this->fechaExpo]);
        $salones = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        include __DIR__ . '/../Views/admin/salones.php';
    }

    public function salonesStore()
    {
        $id = trim($_POST['id'] ?? '');
        $cap = (int) ($_POST['capacidad'] ?? 5);

        // validar
        $errors = [];
        if ($id === '' || !preg_match('/^\d{4}$/', $id)) {
            $errors[] = "ID de salon invalido (4 digitos).";
        }
        if ($cap < 1) {
            $errors[] = "Capacidad debe ser al menos 1.";
        }
        if ($errors) {
            $_SESSION['salon_errors'] = $errors;
            $this->salonesIndex();
            return;
        }

        try {
            $stmt = $this->pdo->prepare("INSERT INTO salones (id, capacidad) VALUES (?,?)");
            $stmt->execute([$id, $cap]);
            $_SESSION['salon_success'] = "Salon $id creado.";
        } catch (\PDOException $e) {
            $_SESSION['salon_errors'] = ["Error: " . $e->getMessage()];
        }

        header('Location: /expoescom/admin/salones');
        exit;
    }

    public function salonesEdit(string $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM salones WHERE id = ?");
        $stmt->execute([$id]);
        $salon = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$salon) {
            header('Location: /expoescom/admin/salones');
            exit;
        }
        include __DIR__ . '/../Views/admin/salones_edit.php';
    }

    public function salonesUpdate(string $id)
    {
        $cap = (int) ($_POST['capacidad'] ?? 5);
        // validar...
        $stmt = $this->pdo->prepare("UPDATE salones SET capacidad = ? WHERE id = ?");
        $stmt->execute([$cap, $id]);
        $_SESSION['salon_success'] = "Salon $id actualizado.";
        header('Location: /expoescom/admin/salones');
        exit;
    }

    public function salonesDelete(string $id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM salones WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['salon_success'] = "Salon $id eliminado.";
        header('Location: /expoescom/admin/salones');
        exit;
    }

    //
    // ---- BLOQUES CRUD ----
    //

    public function bloquesIndex()
    {
        $stmt = $this->pdo->query("SELECT * FROM horarios_bloques ORDER BY id");
        $bloques = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../Views/admin/bloques.php';
    }

    public function bloquesStore()
    {
        $tipo = $_POST['tipo'] ?? '';
        $hi = $_POST['hora_inicio'] ?? '';
        $hf = $_POST['hora_fin'] ?? '';
        // validar...
        $stmt = $this->pdo->prepare("
          INSERT INTO horarios_bloques (id,tipo,hora_inicio,hora_fin)
          VALUES (NULL,?,?,?)");
        $stmt->execute([$tipo, $hi, $hf]);
        $_SESSION['bloque_success'] = "Bloque $tipo creado.";
        header('Location:/expoescom/admin/bloques');
        exit;
    }

    public function bloquesEdit(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM horarios_bloques WHERE id = ?");
        $stmt->execute([$id]);
        $bloque = $stmt->fetch(\PDO::FETCH_ASSOC);
        include __DIR__ . '/../Views/admin/bloques_edit.php';
    }

    public function bloquesUpdate(int $id)
    {
        $tipo = $_POST['tipo'] ?? '';
        $hi = $_POST['hora_inicio'] ?? '';
        $hf = $_POST['hora_fin'] ?? '';
        $stmt = $this->pdo->prepare("
          UPDATE horarios_bloques
          SET tipo=?, hora_inicio=?, hora_fin=?
          WHERE id = ?");
        $stmt->execute([$tipo, $hi, $hf, $id]);
        $_SESSION['bloque_success'] = "Bloque $tipo actualizado.";
        header('Location:/expoescom/admin/bloques');
        exit;
    }

    public function bloquesDelete(int $id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM horarios_bloques WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['bloque_success'] = "Bloque eliminado.";
        header('Location:/expoescom/admin/bloques');
        exit;
    }

    /**
     * 9) Asignar salón y horario
     */
    // public function asignar(int $equipoId)
    // {
    //     try {
    //         // asignarSalon($equipoId, null) => delega el horario si no se pasa
    //         $res = (new AsignacionController)->asignarSalon($equipoId, null);
    //         $_SESSION['success'] = "Salón {$res['salon']} / bloque {$res['bloque']}";
    //     } catch (\Exception $e) {
    //         $_SESSION['errors'] = ["Error al asignar: {$e->getMessage()}"];
    //     }
    //     header('Location: /expoescom/admin');
    //     exit;
    // }
}
