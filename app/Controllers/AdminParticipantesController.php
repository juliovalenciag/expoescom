<?php
namespace App\Controllers;

class AdminParticipantesController
{
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function index()
    {
        // 1. Traer todos los datos de los alumnos, incluyendo equipo y proyecto
        $stmt = $this->pdo->query("
            SELECT 
                a.boleta, a.nombre, a.apellido_paterno, a.apellido_materno, a.telefono,
                a.semestre, a.carrera, a.correo,
                e.nombre_equipo, e.nombre_proyecto, e.es_ganador
            FROM alumnos a
            JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
            JOIN equipos e ON e.id = me.equipo_id
            ORDER BY a.apellido_paterno, a.apellido_materno
        ");
        $participantes = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/admin/participantes.php';
    }

    public function marcarGanador($boleta)
    {
        $stmt = $this->pdo->prepare("
            UPDATE equipos 
            SET es_ganador = 1 
            WHERE id = (
                SELECT equipo_id FROM miembros_equipo WHERE alumno_boleta = ?
            )
        ");
        $stmt->execute([$boleta]);
        header('Location: /expoescom/admin/participantes');
        exit;
    }

    public function apiCreate()
    {


        $data = json_decode(file_get_contents("php://input"), true);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM alumnos WHERE boleta = ?");
        $stmt->execute([$data['boleta']]);
        if ($stmt->fetchColumn() > 0) {
            http_response_code(409);
            echo json_encode(['error' => 'La boleta ya está registrada.']);
            return;
        }
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        try {
            $stmt = $this->pdo->prepare("
    INSERT INTO alumnos (boleta, nombre, apellido_paterno, apellido_materno, curp, genero, telefono, semestre, carrera, correo, password)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");
            $stmt->execute([
                $data['boleta'],
                $data['nombre'],
                $data['apellido_paterno'],
                $data['apellido_materno'],
                $data['curp'],
                $data['genero'],
                $data['telefono'],
                $data['semestre'],
                $data['carrera'],
                $data['correo'],
                password_hash($data['password'], PASSWORD_BCRYPT)
            ]);

            $stmt = $this->pdo->prepare("
            INSERT INTO equipos (nombre_equipo, nombre_proyecto, es_ganador)
            VALUES (?, ?, 0)
        ");
            $stmt->execute([
                "Equipo " . $data['boleta'],
                "Proyecto de " . $data['nombre']
            ]);
            $equipoId = $this->pdo->lastInsertId();

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
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiUpdate(string $boleta)
    {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }

        try {
            $this->pdo->beginTransaction();

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

            // Si hay contraseña nueva válida
            if (!empty($data['password']) && strlen($data['password']) >= 6) {
                $stmt = $this->pdo->prepare("
                UPDATE alumnos SET password = ? WHERE boleta = ?
            ");
                $stmt->execute([
                    password_hash($data['password'], PASSWORD_BCRYPT),
                    $boleta
                ]);
            }

            $this->pdo->commit();

            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function apiDelete(string $boleta)
    {
        $stmt = $this->pdo->prepare("DELETE FROM alumnos WHERE boleta = ?");
        $stmt->execute([$boleta]);

        echo json_encode(['success' => true]);
    }
}
