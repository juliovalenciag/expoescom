<?php
// app/Controllers/SalonController.php
namespace App\Controllers;

class SalonController
{
    /** @var \PDO */
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * 1) Página HTML del gestor (GET /admin/salones)
     */
    public function index()
    {
        include __DIR__ . '/../Views/admin/salones.php';
    }

    // ----- SALONES -----

    /**
     * 2a) API: Listar todos los salones (GET /admin/api/salones)
     */
    public function apiListSalones()
    {
        header('Content-Type: application/json');
        $rows = $this->pdo
            ->query("SELECT id, capacidad FROM salones ORDER BY id")
            ->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($rows);
    }

    /**
     * 2b) API: Crear un salón (POST /admin/api/salones)
     */
    public function apiCreateSalon()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id']) || !isset($data['capacidad'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO salones (id, capacidad) VALUES (?, ?)
            ");
            $stmt->execute([
                $data['id'],
                (int) $data['capacidad']
            ]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * 2c) API: Actualizar un salón (PUT /admin/api/salones/{id})
     */
    public function apiUpdateSalon(string $id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['capacidad'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        try {
            $stmt = $this->pdo->prepare("
                UPDATE salones SET capacidad = ? WHERE id = ?
            ");
            $stmt->execute([
                (int) $data['capacidad'],
                $id
            ]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * 2d) API: Eliminar un salón (DELETE /admin/api/salones/{id})
     */
    public function apiDeleteSalon(string $id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM salones WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // ----- BLOQUES (horarios_bloques) -----

    /**
     * 3a) API: Listar bloques (GET /admin/api/bloques)
     */
    public function apiListBloques()
    {
        header('Content-Type: application/json');
        $rows = $this->pdo
            ->query("SELECT id, tipo, hora_inicio, hora_fin FROM horarios_bloques ORDER BY id")
            ->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode($rows);
    }

    /**
     * 3b) API: Crear bloque (POST /admin/api/bloques)
     */
    public function apiCreateBloque()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['id']) || empty($data['tipo']) || empty($data['hora_inicio']) || empty($data['hora_fin'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO horarios_bloques (id, tipo, hora_inicio, hora_fin)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                (int) $data['id'],
                $data['tipo'],
                $data['hora_inicio'],
                $data['hora_fin']
            ]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * 3c) API: Actualizar bloque (PUT /admin/api/bloques/{id})
     */
    public function apiUpdateBloque(string $id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['tipo']) || empty($data['hora_inicio']) || empty($data['hora_fin'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Datos inválidos']);
            return;
        }
        try {
            $stmt = $this->pdo->prepare("
                UPDATE horarios_bloques
                SET tipo = ?, hora_inicio = ?, hora_fin = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['tipo'],
                $data['hora_inicio'],
                $data['hora_fin'],
                (int) $id
            ]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * 3d) API: Eliminar bloque (DELETE /admin/api/bloques/{id})
     */
    public function apiDeleteBloque(string $id)
    {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM horarios_bloques WHERE id = ?");
            $stmt->execute([(int) $id]);
            echo json_encode(['success' => true]);
        } catch (\PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
