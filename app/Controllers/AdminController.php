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
        // $stmt = $this->pdo->query("SELECT id, nombre FROM academias ORDER BY nombre");
        // $academias = $stmt->fetchAll();

        // // definir IDs que son Matutino
        // $idsMatutino = [/* p.ej. 3,4,6,10 */];
        // foreach ($academias as &$a) {
        //     $a['horarios'] = in_array($a['id'], $idsMatutino, true)
        //         ? ['Matutino']
        //         : ['Vespertino'];
        // }
        // unset($a);

        // // 2) Cargar unidades agrupadas por academia
        // $stmt = $this->pdo->query("SELECT id, nombre, academia_id FROM unidades_aprendizaje ORDER BY nombre");
        // $unidades = $stmt->fetchAll();
        // $unidadesPorAcademia = [];
        // foreach ($unidades as $u) {
        //     $unidadesPorAcademia[$u['academia_id']][] = [
        //         'id' => $u['id'],
        //         'nombre' => $u['nombre'],
        //     ];
        // }

        // 3) Pasar todo a la vista
        include __DIR__ . '/../Views/admin/participantes.php';
    }
    /**
     * 8a) API: obtener todos los participantes
     */
    public function apiList()
    {
        $stmt = $this->pdo->query("
        SELECT a.boleta, a.nombre, a.apellido_paterno, a.apellido_materno, a.correo, a.telefono,
               a.semestre, a.carrera, e.nombre_equipo, e.nombre_proyecto, e.es_ganador
        FROM alumnos a
        LEFT JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
        LEFT JOIN equipos e ON e.id = me.equipo_id
    ");
        echo json_encode($stmt->fetchAll());
    }
    /**
     * 8b) API: crear participante + equipo + miembro
     */
    public function apiCreate()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Insertar alumno
            $stmt = $this->pdo->prepare("
            INSERT INTO alumnos (boleta, nombre, apellido_paterno, apellido_materno, genero, telefono, semestre, carrera, correo, password)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
            $stmt->execute([
                $data['boleta'],
                $data['nombre'],
                $data['apellido_paterno'],
                $data['apellido_materno'],
                $data['genero'],
                $data['telefono'],
                $data['semestre'],
                $data['carrera'],
                $data['correo'],
                password_hash($data['password'], PASSWORD_BCRYPT)
            ]);

            // 2. Crear equipo (con nombre temporal si no se ingresa otro)
            $nombreEquipo = "Equipo " . $data['boleta'];
            $nombreProyecto = "Proyecto de " . $data['nombre'];
            $stmt = $this->pdo->prepare("
            INSERT INTO equipos (nombre_equipo, nombre_proyecto, es_ganador)
            VALUES (?, ?, 0)
        ");
            $stmt->execute([$nombreEquipo, $nombreProyecto]);
            $equipoId = $this->pdo->lastInsertId();

            // 3. Insertar en miembros_equipo
            $stmt = $this->pdo->prepare("
            INSERT INTO miembros_equipo (alumno_boleta, equipo_id)
            VALUES (?, ?)
        ");
            $stmt->execute([$data['boleta'], $equipoId]);

            $this->pdo->commit();

            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Error al guardar: ' . $e->getMessage()]);
        }
    }

    /**
     * 8c) API: actualizar alumno
     */
    public function apiUpdate(string $boleta)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        try {
            if (!empty($data['password'])) {
                $stmt = $this->pdo->prepare("
        UPDATE alumnos SET
            nombre = ?, apellido_paterno = ?, apellido_materno = ?,
            genero = ?, telefono = ?, semestre = ?, carrera = ?, correo = ?, password = ?
        WHERE boleta = ?
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
            nombre = ?, apellido_paterno = ?, apellido_materno = ?,
            genero = ?, telefono = ?, semestre = ?, carrera = ?, correo = ?
        WHERE boleta = ?
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
            echo json_encode(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }

    /**
     * 8d) API: eliminar alumno
     */
    public function apiDelete(string $boleta)
    {
        $stmt = $this->pdo->prepare("DELETE FROM alumnos WHERE boleta = ?");
        $stmt->execute([$boleta]);

        echo json_encode(['success' => true]);
    }

    /**
     * 8e) API: alternar ganador
     */
    public function apiToggleWinner(string $boleta)
    {
        // obtener equipo_id
        $stmt = $this->pdo->prepare("
        SELECT equipo_id FROM miembros_equipo WHERE alumno_boleta = ?
    ");
        $stmt->execute([$boleta]);
        $equipoId = $stmt->fetchColumn();

        if (!$equipoId) {
            http_response_code(404);
            echo json_encode(['error' => 'No se encontró el equipo']);
            return;
        }

        // alternar el valor de ganador
        $stmt = $this->pdo->prepare("
        UPDATE equipos
        SET es_ganador = NOT es_ganador
        WHERE id = ?
    ");
        $stmt->execute([$equipoId]);

        echo json_encode(['success' => true]);
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
