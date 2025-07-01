<?php
namespace App\Controllers;

class EquipoController
{
    protected $pdo;
    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /** GET /expoescom/api/equipos?search=xxx **/
    public function search()
    {
        $q = trim($_GET['search'] ?? '');
        header('Content-Type: application/json; charset=UTF-8');

        if ($q === '') {
            echo json_encode([]);
            return;
        }

        $stmt = $this->pdo->prepare("
            SELECT id, nombre_equipo 
              FROM equipos 
             WHERE nombre_equipo LIKE ? 
             ORDER BY nombre_equipo 
             LIMIT 10
        ");
        $stmt->execute(["%{$q}%"]);
        echo json_encode($stmt->fetchAll(\PDO::FETCH_ASSOC));
    }
}
