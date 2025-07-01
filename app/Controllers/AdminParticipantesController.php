<?php
namespace App\Controllers;

class AdminParticipantesController
{
    /** @var \PDO */
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * GET /admin/participantes
     * Muestra la página con la tabla y modales
     */
    public function index()
    {
        include __DIR__ . '/../Views/admin/participantes.php';
        exit;
    }

    /**
     * GET /admin/api/participantes
     * Devuelve JSON con todos los participantes + datos relacionados
     */
    public function apiList()
    {
        $stmt = $this->pdo->query("
            SELECT 
              a.boleta,
              a.nombre,
              a.apellido_paterno,
              a.apellido_materno,
              a.genero,
              a.telefono,
              a.semestre,
              a.carrera,
              a.correo,
              e.nombre_equipo,
              e.nombre_proyecto,
              e.es_ganador,
              ac.nombre AS academia,
              ua.nombre AS unidad
            FROM alumnos a
            LEFT JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
            LEFT JOIN equipos e         ON e.id              = me.equipo_id
            LEFT JOIN academias ac      ON ac.id             = e.academia_id
            LEFT JOIN unidades_aprendizaje ua ON ua.id        = me.unidad_id
            ORDER BY a.apellido_paterno, a.nombre
        ");
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }

    /**
     * POST /admin/api/participantes
     * Crea un nuevo participante (y equipo si no existe)
     */
    public function apiCreate()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            return json_encode(['error' => 'Payload invalido']);
        }

        // Validaciones mínimas
        $errors = [];
        if (!preg_match('/^(?:\d{10}|(?:PE|PP)\d{8})$/', $data['boleta'] ?? '')) {
            $errors[] = 'Boleta invalida';
        }
        if (
            !filter_var($data['correo'] ?? '', FILTER_VALIDATE_EMAIL) ||
            !str_ends_with($data['correo'], '@alumno.ipn.mx')
        ) {
            $errors[] = 'Correo invalido';
        }
        // ... (aquí podrías añadir más validaciones de nombre, telefono, etc.)

        if ($errors) {
            http_response_code(422);
            return json_encode(['errors' => $errors]);
        }

        try {
            $this->pdo->beginTransaction();

            // 1) Insertar alumno
            $stmt = $this->pdo->prepare("
                INSERT INTO alumnos
                  (boleta,nombre,apellido_paterno,apellido_materno,
                   genero,curp,telefono,semestre,carrera,correo,password)
                VALUES (?,?,?,?,?,?,?,?,?,?,?)
            ");
            // curp y password arbitrarios o vacíos: admin luego puede pedir reset
            $iv = str_repeat("\0", openssl_cipher_iv_length('AES-256-CBC'));
            $dummyCurp = openssl_encrypt('XXXXXXXXXXXXXX', 'AES-256-CBC', 'TU_LLAVE_DE_AES', OPENSSL_RAW_DATA, $iv);
            $stmt->execute([
                $data['boleta'],
                $data['nombre'],
                $data['apellido_paterno'],
                $data['apellido_materno'],
                $data['genero'],
                $dummyCurp,
                $data['telefono'],
                $data['semestre'],
                $data['carrera'],
                $data['correo'],
                password_hash($data['password'] ?? bin2hex(random_bytes(4)), PASSWORD_BCRYPT)
            ]);

            // 2) Equipo: si no viene ID, crear uno genérico
            if (!empty($data['equipo_id'])) {
                $equipoId = (int) $data['equipo_id'];
            } else {
                $stmt = $this->pdo->prepare("
                    INSERT INTO equipos 
                      (nombre_equipo,nombre_proyecto,academia_id,horario_preferencia)
                    VALUES (?,?,?,?)
                ");
                $stmt->execute([
                    $data['nombre_equipo'] ?? 'Equipo ' . $data['boleta'],
                    $data['nombre_proyecto'] ?? 'Proyecto ' . $data['boleta'],
                    (int) ($data['academia_id'] ?? 1),
                    $data['horario_preferencia'] ?? 'Matutino'
                ]);
                $equipoId = (int) $this->pdo->lastInsertId();
            }

            // 3) Miembros_equipo
            $stmt = $this->pdo->prepare("
                INSERT INTO miembros_equipo (alumno_boleta,equipo_id,unidad_id)
                VALUES (?,?,?)
            ");
            $stmt->execute([
                $data['boleta'],
                $equipoId,
                (int) ($data['unidad_id'] ?? 1)
            ]);

            $this->pdo->commit();
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * PUT /admin/api/participantes/{boleta}
     * Actualiza datos de un participante
     */
    public function apiUpdate(string $boleta)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!$data) {
            http_response_code(400);
            return;
        }

        // Preparar campos dinámicos
        $fields = [];
        $params = [];
        foreach (['nombre', 'apellido_paterno', 'apellido_materno', 'genero', 'telefono', 'semestre', 'carrera', 'correo'] as $f) {
            if (isset($data[$f])) {
                $fields[] = "$f = ?";
                $params[] = $data[$f];
            }
        }
        if (!empty($data['password'])) {
            $fields[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }

        if (empty($fields)) {
            echo json_encode(['success' => false, 'msg' => 'Nada para actualizar']);
            return;
        }

        $params[] = $boleta;
        try {
            $stmt = $this->pdo->prepare("
                UPDATE alumnos SET " . implode(',', $fields) . " WHERE boleta = ?
            ");
            $stmt->execute($params);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * DELETE /admin/api/participantes/{boleta}
     */
    public function apiDelete(string $boleta)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM alumnos WHERE boleta = ?");
            $stmt->execute([$boleta]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * POST /admin/api/participantes/{boleta}/toggle-winner
     */
    public function apiToggleWinner(string $boleta)
    {
        // 1) Obtener equipo_id
        $stmt = $this->pdo->prepare("SELECT equipo_id FROM miembros_equipo WHERE alumno_boleta = ?");
        $stmt->execute([$boleta]);
        $equipoId = $stmt->fetchColumn();
        if (!$equipoId) {
            http_response_code(404);
            return json_encode(['error' => 'Equipo no encontrado']);
        }
        // 2) Alternar
        $stmt = $this->pdo->prepare("UPDATE equipos SET es_ganador = NOT es_ganador WHERE id = ?");
        $stmt->execute([$equipoId]);
        echo json_encode(['success' => true]);
    }
}
