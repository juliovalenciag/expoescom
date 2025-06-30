<?php
namespace App\Controllers;

require_once __DIR__ . '/../../lib/fpdf/fpdf.php';
require_once __DIR__ . '/../../lib/fpdf/pdfCabecera.php';

class PDFController
{
    protected $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function acuse(string $boleta)
    {
        // 1) Traer datos del alumno y su asignación
        $stmt = $this->pdo->prepare("
        SELECT 
            a.boleta,
            a.nombre,
            a.apellido_paterno,
            a.apellido_materno,
            e.nombre_equipo,
            e.nombre_proyecto,
            s.salon_id,
            hb.tipo AS bloque,
            hb.hora_inicio,
            hb.hora_fin,
            s.fecha
        FROM alumnos a
        JOIN miembros_equipo me   ON me.alumno_boleta = a.boleta
        JOIN equipos e            ON e.id              = me.equipo_id
        JOIN asignaciones s       ON s.equipo_id       = e.id
        JOIN horarios_bloques hb  ON hb.id             = s.horario_id
        WHERE a.boleta = ?
    ");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch();

        if (!$info) {
            die("No se encontro asignación para la boleta $boleta");
        }

        // 2) Crear el PDF
        $pdf = new \PDFCabecera();
        $pdf->AddPage();

        // 3) Cabecera
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Ln(45); // bajar para no tapar encabezado
        $pdf->Cell(0, 10, "Acuse de Registro - ExpoESCOM 2025", 0, 1, 'C');
        $pdf->Ln(5);

        // 4) Datos personales
        $pdf->SetFont('Arial', '', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(50, 8, "Boleta:", 0, 0);
        $pdf->Cell(0, 8, $info['boleta'], 0, 1);
        $pdf->Cell(50, 8, "Nombre:", 0, 0);
        $pdf->Cell(0, 8, "{$info['nombre']} {$info['apellido_paterno']} {$info['apellido_materno']}", 0, 1);
        $pdf->Ln(4);

        // 5) Bloque de datos resaltados
        $pdf->SetFillColor(0, 91, 187); // #005BBB
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(50, 8, "Salon:", 0, 0, 'L', true);
        $pdf->Cell(0, 8, $info['salon_id'], 0, 1, 'L', true);
        $pdf->Cell(50, 8, "Bloque:", 0, 0, 'L', true);
        $pdf->Cell(0, 8, $info['bloque'], 0, 1, 'L', true);
        $pdf->Cell(50, 8, "Fecha:", 0, 0, 'L', true);
        $pdf->Cell(0, 8, $info['fecha'], 0, 1, 'L', true);
        $pdf->Cell(50, 8, "Horario:", 0, 0, 'L', true);
        $pdf->Cell(0, 8, "{$info['hora_inicio']} a {$info['hora_fin']}", 0, 1, 'L', true);
        $pdf->Ln(1);
        $pdf->Cell(50, 8, "Equipo:", 0, 0, 'L', true);
        $pdf->Cell(0, 8, $info['nombre_equipo'], 0, 1, 'L', true);
        $pdf->Cell(50, 8, "Proyecto:", 0, 0, 'L', true);
        $pdf->Cell(0, 8, $info['nombre_proyecto'], 0, 1, 'L', true);
        $pdf->Ln(10);

        // 6) Texto adicional y firma
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'I', 11);
        $pdf->MultiCell(
            0,
            8,
            "Este documento certifica que el alumno ha sido registrado correctamente para la presentacion de su proyecto en ExpoESCOM 2025.",
            0,
            'L'
        );

        
        // 7) Descargar
        $pdf->Output('D', "Acuse_{$boleta}.pdf");
        exit;
    }


    public function diploma(string $boleta)
    {
        $stmt = $this->pdo->prepare("
            SELECT 
              a.boleta,
              a.nombre,
              a.apellido_paterno,
              a.apellido_materno,
              e.nombre_proyecto,
              e.es_ganador
            FROM alumnos a
            JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
            JOIN equipos e         ON e.id              = me.equipo_id
            WHERE a.boleta = ?
        ");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch();

        if (!$info || !$info['es_ganador']) {
            die("No estás marcado como ganador.");
        }

        $pdf = new \PDFCabecera();
        $pdf->AddPage('L'); // horizontal
        $pdf->Ln(25);

        $pdf->SetFont('Arial', 'B', 22);
        $pdf->Cell(0, 20, ("Diploma de Reconocimiento"), 0, 1, 'C');

        $pdf->Ln(10);
        $pdf->SetFont('Arial', '', 16);
        $pdf->MultiCell(0, 10, ("Otorgado a:\n{$info['nombre']} {$info['apellido_paterno']} {$info['apellido_materno']}"), 0, 'C');
        $pdf->Ln(5);

        $pdf->SetFont('Arial', 'I', 14);
        $pdf->MultiCell(0, 8, ("Por su destacada participacion y esfuerzo en la ExpoESCOM 2025 con el proyecto:\n\"{$info['nombre_proyecto']}\""), 0, 'C');
        $pdf->Ln(20);

    
        $pdf->Output('D', "Diploma_{$boleta}.pdf");
        exit;
    }
}
