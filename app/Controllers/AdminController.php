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
        $totales = [
            'alumnos' => (int) $this->pdo->query("SELECT COUNT(*) FROM alumnos")->fetchColumn(),
            'equipos' => (int) $this->pdo->query("SELECT COUNT(*) FROM equipos")->fetchColumn(),
            'ganadores' => (int) $this->pdo->query("SELECT COUNT(*) FROM equipos WHERE es_ganador = 1")->fetchColumn(),
        ];

        include __DIR__ . '/../Views/admin/dashboard.php';
        exit;
    }

    /**
     * 7) Vista HTML: listado de participantes
     */

    public function list()
    {
        // 1) Cargar academias con su info de horario
        $stmt = $this->pdo->query("SELECT id, nombre FROM academias ORDER BY nombre");
        $academias = $stmt->fetchAll();

        // definir IDs que son Matutino
        $idsMatutino = [/* p.ej. 3,4,6,10 */];
        foreach ($academias as &$a) {
            $a['horarios'] = in_array($a['id'], $idsMatutino, true)
                ? ['Matutino']
                : ['Vespertino'];
        }
        unset($a);

        // 2) Cargar unidades agrupadas por academia
        $stmt = $this->pdo->query("SELECT id, nombre, academia_id FROM unidades_aprendizaje ORDER BY nombre");
        $unidades = $stmt->fetchAll();
        $unidadesPorAcademia = [];
        foreach ($unidades as $u) {
            $unidadesPorAcademia[$u['academia_id']][] = [
                'id' => $u['id'],
                'nombre' => $u['nombre'],
            ];
        }

        // 3) Pasar todo a la vista
        include __DIR__ . '/../Views/admin/participantes.php';
    }
    /**
     * 8a) API: obtener todos los participantes
     */
    public function apiList()
    {
        session_start();
        header('Content-Type: application/json; charset=utf-8');

        // DEBUG
        echo json_encode([
            'admin' => $_SESSION['admin'] ?? null,
            'session_id' => session_id()
        ]);


        try {
            $sql = "
        SELECT
          a.boleta,
          a.nombre,
          a.apellido_paterno,
          a.apellido_materno,
          a.curp,
          a.genero,
          a.telefono,
          a.semestre,
          a.carrera,
          a.correo,
          e.id                   AS equipo_id,
          e.nombre_equipo,
          e.nombre_proyecto,
          e.horario_preferencia,
          e.academia_id,
          me.unidad_id,
          e.es_ganador,
          s.salon_id,
          hb.tipo                AS bloque,
          hb.hora_inicio,
          hb.hora_fin
        FROM alumnos a
        JOIN miembros_equipo me   ON me.alumno_boleta = a.boleta
        JOIN equipos e           ON e.id            = me.equipo_id
        LEFT JOIN asignaciones s ON s.equipo_id     = e.id
        LEFT JOIN horarios_bloques hb 
          ON hb.id = s.horario_id
        ORDER BY a.boleta
        ";

            $rows = $this->pdo->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            echo json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * 8b) API: crear participante + equipo + miembro
     */
    public function apiCreate()
    {
        header('Content-Type: application/json; charset=utf-8');
        $data = json_decode(file_get_contents('php://input'), true) ?: [];

        $required = [
            'boleta',
            'nombre',
            'apellido_paterno',
            'apellido_materno',
            'genero',
            'telefono',
            'semestre',
            'carrera',
            'correo',
            'nombre_equipo',
            'nombre_proyecto',
            'academia_id',
            'unidad_id',
            'horario_preferencia'
        ];
        foreach ($required as $f) {
            if (empty($data[$f])) {
                http_response_code(422);
                echo json_encode(['success' => false, 'error' => "Falta el campo: {$f}"]);
                return;
            }
        }

        try {
            $this->pdo->beginTransaction();

            // 1) Alumno (curp/password vacío)
            $stmt = $this->pdo->prepare("
                INSERT INTO alumnos 
                  (boleta,nombre,apellido_paterno,apellido_materno,
                   genero,curp,telefono,semestre,carrera,correo,password)
                VALUES 
                  (:boleta,:nombre,:ap,:am,:genero,'',:telefono,:semestre,:carrera,:correo,'')
            ");
            $stmt->execute([
                ':boleta' => $data['boleta'],
                ':nombre' => $data['nombre'],
                ':ap' => $data['apellido_paterno'],
                ':am' => $data['apellido_materno'],
                ':genero' => $data['genero'],
                ':telefono' => $data['telefono'],
                ':semestre' => (int) $data['semestre'],
                ':carrera' => $data['carrera'],
                ':correo' => $data['correo'],
            ]);

            // 2) Equipo
            $stmt = $this->pdo->prepare("
                INSERT INTO equipos 
                  (nombre_equipo,nombre_proyecto,academia_id,horario_preferencia)
                VALUES 
                  (:ne,:np,:aid,:hp)
            ");
            $stmt->execute([
                ':ne' => $data['nombre_equipo'],
                ':np' => $data['nombre_proyecto'],
                ':aid' => (int) $data['academia_id'],
                ':hp' => $data['horario_preferencia'],
            ]);
            $equipoId = (int) $this->pdo->lastInsertId();

            // 3) Miembro
            $stmt = $this->pdo->prepare("
                INSERT INTO miembros_equipo 
                  (alumno_boleta,equipo_id,unidad_id)
                VALUES 
                  (:boleta,:eid,:uid)
            ");
            $stmt->execute([
                ':boleta' => $data['boleta'],
                ':eid' => $equipoId,
                ':uid' => (int) $data['unidad_id'],
            ]);

            $this->pdo->commit();
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Creación exitosa']);
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * 8c) API: actualizar alumno
     */
    public function apiUpdate(string $boleta)
    {
        header('Content-Type: application/json; charset=utf-8');
        $data = json_decode(file_get_contents('php://input'), true) ?: [];

        if (empty($data['nombre']) || empty($data['correo'])) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => 'Nombre y correo obligatorios']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("
                UPDATE alumnos SET
                  nombre           = :nombre,
                  apellido_paterno = :ap,
                  apellido_materno = :am,
                  genero           = :genero,
                  telefono         = :telefono,
                  semestre         = :semestre,
                  carrera          = :carrera,
                  correo           = :correo
                WHERE boleta = :boleta
            ");
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':ap' => $data['apellido_paterno'] ?? '',
                ':am' => $data['apellido_materno'] ?? '',
                ':genero' => $data['genero'] ?? 'Otro',
                ':telefono' => $data['telefono'] ?? '',
                ':semestre' => (int) ($data['semestre'] ?? 1),
                ':carrera' => $data['carrera'] ?? 'ISC',
                ':correo' => $data['correo'],
                ':boleta' => $boleta
            ]);
            echo json_encode(['success' => true, 'message' => 'Actualización exitosa']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * 8d) API: eliminar alumno
     */
    public function apiDelete(string $boleta)
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $stmt = $this->pdo->prepare("DELETE FROM alumnos WHERE boleta = ?");
            $stmt->execute([$boleta]);
            echo json_encode(['success' => true, 'message' => 'Eliminación exitosa']);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * 8e) API: alternar ganador
     */
    public function apiToggleWinner(string $boleta)
    {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $stmt = $this->pdo->prepare("
                SELECT e.id, e.es_ganador
                  FROM miembros_equipo me
                  JOIN equipos e ON e.id = me.equipo_id
                 WHERE me.alumno_boleta = ?
            ");
            $stmt->execute([$boleta]);
            $row = $stmt->fetch();
            if (!$row)
                throw new \Exception("Equipo no encontrado");

            $nuevo = $row['es_ganador'] ? 0 : 1;
            $this->pdo
                ->prepare("UPDATE equipos SET es_ganador = ? WHERE id = ?")
                ->execute([$nuevo, $row['id']]);

            echo json_encode(['success' => true, 'es_ganador' => (bool) $nuevo]);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    /**
     * 9) Asignar salón y horario
     */
    public function asignar(int $equipoId)
    {
        try {
            // asignarSalon($equipoId, null) => delega el horario si no se pasa
            $res = (new AsignacionController)->asignarSalon($equipoId, null);
            $_SESSION['success'] = "Salón {$res['salon']} / bloque {$res['bloque']}";
        } catch (\Exception $e) {
            $_SESSION['errors'] = ["Error al asignar: {$e->getMessage()}"];
        }
        header('Location: /expoescom/admin');
        exit;
    }
}
