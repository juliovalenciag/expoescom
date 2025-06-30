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

    // Agrega funciones para editar, eliminar y crear si lo necesitas aquí.
}
