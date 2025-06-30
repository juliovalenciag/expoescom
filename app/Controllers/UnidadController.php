<?php
namespace App\Controllers;

class UnidadController
{
    protected $pdo;
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    // GET /expoescom/api/unidades?academia=ID
    public function getByAcademia()
    {
        $id = $_GET['academia'] ?? '';
        $stmt = $this->pdo->prepare("
            SELECT id, nombre 
              FROM unidades_aprendizaje 
             WHERE academia_id = ? 
             ORDER BY nombre
        ");
        $stmt->execute([$id]);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($stmt->fetchAll());
        exit;
    }
}
