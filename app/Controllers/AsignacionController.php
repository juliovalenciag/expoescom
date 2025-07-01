<?php
namespace App\Controllers;

class AsignacionController
{
    protected $pdo;
    
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
     * @return string salón asignado (p.ej. '4012')
     * @throws \Exception si no hay espacio
     */
    public function asignarSalon(int $equipoId, int $horarioId): string
    {
        // Obtener salones disponibles ordenados
        $stmt = $this->pdo->query("SELECT id, capacidad FROM salones ORDER BY id");
        $salones = $stmt->fetchAll();

        // Define bloques por tipo (matutino o vespertino)
        $horasBase = $horarioId === 1 ? '10:30' : '15:00';
        $horaBase = new \DateTime($horasBase);

        $check = $this->pdo->prepare("
        SELECT COUNT(*) FROM asignaciones
         WHERE salon_id = ?
           AND horario_id = ?
           AND fecha = ?
    ");

        $insert = $this->pdo->prepare("
        INSERT INTO asignaciones
            (equipo_id, salon_id, horario_id, fecha, hora_inicio, hora_fin)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

        foreach ($salones as $salon) {
            // Contar cuántos hay ya asignados
            $check->execute([$salon['id'], $horarioId, $this->fechaExpo]);
            $asignados = (int) $check->fetchColumn();

            if ($asignados < 3) {
                $inicio = clone $horaBase;
                $inicio->modify('+' . (90 * $asignados) . ' minutes'); // Cada equipo 90min
                $fin = clone $inicio;
                $fin->modify('+60 minutes'); // 60 min exposición

                $insert->execute([
                    $equipoId,
                    $salon['id'],
                    $horarioId,
                    $this->fechaExpo,
                    $inicio->format('H:i:s'),
                    $fin->format('H:i:s')
                ]);

                return $salon['id'];
            }
        }

        throw new \Exception("No hay salones disponibles en este bloque horario.");
    }

}
