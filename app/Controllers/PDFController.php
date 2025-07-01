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
        // 1) Traer datos
        $stmt = $this->pdo->prepare("
            SELECT 
                a.boleta,
                a.nombre,
                a.apellido_paterno,
                a.apellido_materno,
                e.nombre_equipo,
                e.nombre_proyecto,
                ac.nombre       AS academia,
                ua.nombre       AS unidad,
                s.salon_id,
                hb.tipo         AS bloque,
                hb.hora_inicio,
                hb.hora_fin,
                s.fecha
            FROM alumnos a
            JOIN miembros_equipo me    ON me.alumno_boleta = a.boleta
            JOIN equipos e             ON e.id            = me.equipo_id
            JOIN academias ac          ON ac.id           = e.academia_id
            JOIN unidades_aprendizaje ua ON ua.id          = me.unidad_id
            JOIN asignaciones s        ON s.equipo_id     = e.id
            JOIN horarios_bloques hb   ON hb.id           = s.horario_id
            WHERE a.boleta = ?
        ");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch();

        if (!$info) {
            die("No se encontro asignacion para la boleta $boleta");
        }

        // 2) Crear PDF
        $pdf = new \PDFCabecera();
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 20);

        // Espacio extra al inicio
        $pdf->Ln(20);

        // 3) Titulo principal
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Cell(0, 12, "ACUSE DE REGISTRO", 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 6, "ExpoESCOM 2025", 0, 1, 'C');
        $pdf->Ln(10);

        // 4) Texto descriptivo
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->SetTextColor(80);
        $pdf->MultiCell(
            0,
            6,
            "Este documento certifica que el alumno ha sido registrado correctamente " .
            "para presentacion de su proyecto en ExpoESCOM 2025."
        );
        $pdf->Ln(8);

        // 5) Datos personales
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(40, 6, "Boleta:", 0, 0);
        $pdf->Cell(60, 6, $info['boleta'], 0, 0);
        $pdf->Cell(40, 6, "Fecha:", 0, 0);
        $pdf->Cell(0, 6, $info['fecha'], 0, 1);
        $pdf->Ln(3);

        $pdf->Cell(40, 6, "Nombre:", 0, 0);
        $pdf->Cell(0, 6, "{$info['nombre']} {$info['apellido_paterno']} {$info['apellido_materno']}", 0, 1);
        $pdf->Ln(6);

        // 6) Cuadro de asignacion
        $pdf->SetFillColor(0, 91, 187);
        $pdf->SetTextColor(255);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(50, 7, "SALON", 1, 0, 'C', true);
        $pdf->Cell(0, 7, $info['salon_id'], 1, 1, 'C', true);

        $pdf->Cell(50, 7, "BLOQUE", 1, 0, 'C', true);
        $pdf->Cell(0, 7, $info['bloque'], 1, 1, 'C', true);

        $pdf->Cell(50, 7, "HORARIO", 1, 0, 'C', true);
        $pdf->Cell(0, 7, "{$info['hora_inicio']} - {$info['hora_fin']}", 1, 1, 'C', true);
        $pdf->Ln(6);

        // 7) Academica / Unidad
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 6, "Academia:", 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $info['academia'], 0, 1);
        $pdf->Ln(2);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(30, 6, "Unidad:", 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $info['unidad'], 0, 1);
        $pdf->Ln(8);

        // 8) Equipo & Proyecto (proyecto en grande)
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0, 91, 187);
        $pdf->Cell(30, 6, "Equipo:", 0, 0);
        $pdf->SetTextColor(0);
        $pdf->Cell(0, 6, $info['nombre_equipo'], 0, 1);
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(30, 6, "Proyecto:", 0, 0);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Cell(0, 8, strtoupper($info['nombre_proyecto']), 0, 1);
        $pdf->Ln(10);

        // Limpia buffer antes de enviar
        if (ob_get_length()) {
            ob_end_clean();
        }

        // 9) Descargar
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
                e.es_ganador,
                ac.nombre AS academia,
                ua.nombre AS unidad
            FROM alumnos a
            JOIN miembros_equipo me    ON me.alumno_boleta = a.boleta
            JOIN equipos e             ON e.id            = me.equipo_id
            JOIN academias ac          ON ac.id           = e.academia_id
            JOIN unidades_aprendizaje ua ON ua.id          = me.unidad_id
            WHERE a.boleta = ?
        ");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch();

        if (!$info || !$info['es_ganador']) {
            die("No estas marcado como ganador.");
        }

        $pdf = new \PDFCabecera();
        $pdf->AddPage('L');
        $pdf->SetAutoPageBreak(true, 20);

        // Espacio extra al inicio
        $pdf->Ln(30);

        // Titulo Diploma
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->SetTextColor(0, 51, 102);
        $pdf->Cell(0, 12, "DIPLOMA DE RECONOCIMIENTO", 0, 1, 'C');
        $pdf->Ln(12);

        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(0, 10, ("Otorgado a:\n"), 0, 'C');

        // Nombre
        $pdf->SetFont('Arial', '', 16);
        $pdf->Cell(0, 8, "{$info['nombre']} {$info['apellido_paterno']} {$info['apellido_materno']}", 0, 1, 'C');
        $pdf->Ln(8);

        $pdf->SetFont('Arial', 'I', 14);
        $pdf->MultiCell(0, 8, ("Por su destacada participacion y esfuerzo en la ExpoESCOM 2025 con el proyecto:\n"), 0, 'C');
        $pdf->Ln(10);
        // Proyecto en negrita grande
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(0, 91, 187);
        $pdf->Cell(0, 10, strtoupper($info['nombre_proyecto']), 0, 1, 'C');
        $pdf->Ln(10);

        // Academia y Unidad
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(0);
        $pdf->Cell(40, 6, "Academia:", 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 6, $info['academia'], 0, 1);
        $pdf->Ln(4);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(40, 6, "Unidad:", 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 6, $info['unidad'], 0, 1);
        $pdf->Ln(12);

        // Limpia buffer y descarga
        if (ob_get_length()) {
            ob_end_clean();
        }
        $pdf->Output('D', "Diploma_{$boleta}.pdf");
        exit;
    }
}
