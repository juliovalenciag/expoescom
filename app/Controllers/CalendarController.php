<?php
namespace App\Controllers;

class CalendarController
{
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * GET /api/calendar
     * Devuelve en JSON los eventos (tu exposición) para FullCalendar.
     */
    public function events()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $boleta = $_SESSION['alumno_boleta'] ?? null;
        if (!$boleta) {
            http_response_code(401);
            echo json_encode([]);
            exit;
        }

        $stmt = $this->pdo->prepare("
            SELECT s.fecha, hb.hora_inicio, hb.hora_fin, s.salon_id
              FROM alumnos a
              JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
              JOIN asignaciones s       ON s.equipo_id       = me.equipo_id
              JOIN horarios_bloques hb  ON hb.id             = s.horario_id
             WHERE a.boleta = ?
        ");
        $stmt->execute([$boleta]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        $events = [];
        if ($row) {
            $events[] = [
                'title' => "Expo (Salón {$row['salon_id']})",
                'start' => "{$row['fecha']}T{$row['hora_inicio']}",
                'end' => "{$row['fecha']}T{$row['hora_fin']}"
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($events);
        exit;
    }
}
