<?php
namespace App\Controllers;

class AsignacionController
{
    protected $pdo;
    // Fecha fija del evento (ajústala si hace falta)
    protected $fechaExpo = '2025-10-15';

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    /**
     * Asigna el primer salón disponible para el equipo dado y bloque horario.
     * @param int $equipoId
     * @param int $horarioId  (1=Matutino, 2=Vespertino)
     * @return string salón asignado (p.ej. 'A1')
     * @throws \Exception si no hay espacio
     */
    public function asignarSalon(int $equipoId, int $horarioId): string
    {
        // 1) Obtener todos los salones
        $stmt = $this->pdo->query("SELECT id, capacidad FROM salones ORDER BY id");
        $salones = $stmt->fetchAll();

        // 2) Revisar capacidad de cada uno
        $check = $this->pdo->prepare("
            SELECT COUNT(*) 
              FROM asignaciones 
             WHERE salon_id = ? 
               AND horario_id = ? 
               AND fecha = ?
        ");

        $insert = $this->pdo->prepare("
            INSERT INTO asignaciones (equipo_id, salon_id, horario_id, fecha)
            VALUES (?, ?, ?, ?)
        ");

        foreach ($salones as $salon) {
            $check->execute([$salon['id'], $horarioId, $this->fechaExpo]);
            if ($check->fetchColumn() < $salon['capacidad']) {
                // Hay espacio: hacemos la asignación
                $insert->execute([$equipoId, $salon['id'], $horarioId, $this->fechaExpo]);
                return $salon['id'];
            }
        }

        throw new \Exception("No hay salones disponibles en ese bloque horario.");
    }
}
