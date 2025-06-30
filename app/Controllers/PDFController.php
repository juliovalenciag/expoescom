<?php
namespace App\Controllers;

// Asegúrate de que estas rutas coincidan con tu estructura en /lib/fpdf/
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

    /**
     * Genera y descarga el Acuse en PDF para la boleta indicada.
     * URL de ejemplo: /expoescom/pdf/acuse/2020123456
     */
    public function acuse(string $boleta)
    {
        // 1) Traer datos del alumno y su asignación
        $stmt = $this->pdo->prepare("
            SELECT 
                a.boleta,
                a.nombre,
                s.salon_id,
                hb.tipo AS bloque,
                hb.hora_inicio,
                hb.hora_fin
            FROM alumnos a
            JOIN miembros_equipo me   ON me.alumno_boleta = a.boleta
            JOIN asignaciones s       ON s.equipo_id       = me.equipo_id
            JOIN horarios_bloques hb  ON hb.id             = s.horario_id
            WHERE a.boleta = ?
        ");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch();

        if (!$info) {
            die("No se encontró asignación para la boleta $boleta");
        }

        // 2) Crear el PDF usando tu clase PDFCabecera
        $pdf = new \PDFCabecera();
        $pdf->AddPage();

        // Cabecera automática en PDFCabecera (logos/márgenes)
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, "Acuse de Registro ExpoESCOM 2025", 0, 1, 'C');
        $pdf->Ln(5);

        // 3) Contenido: datos del alumno
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(50, 8, "Boleta:", 0, 0);
        $pdf->Cell(0, 8, $info['boleta'], 0, 1);
        $pdf->Cell(50, 8, "Nombre:", 0, 0);
        $pdf->Cell(
            0,
            8,
            utf8_decode("{$info['nombre']} {$info['apellido_paterno']} {$info['apellido_materno']}"),
            0,
            1
        );
        $pdf->Ln(4);

        // 4) Asignación de salón/horario
        $pdf->Cell(50, 8, "Salón:", 0, 0);
        $pdf->Cell(0, 8, $info['salon_id'], 0, 1);
        $pdf->Cell(50, 8, "Bloque:", 0, 0);
        $pdf->Cell(0, 8, $info['bloque'], 0, 1);
        $pdf->Cell(50, 8, "Horario:", 0, 0);
        $pdf->Cell(0, 8, "{$info['hora_inicio']} - {$info['hora_fin']}", 0, 1);
        $pdf->Ln(8);

        // 5) Pie de página automático de PDFCabecera con logos

        // 6) Enviar al navegador
        $pdf->Output('D', "Acuse_{$boleta}.pdf");
        exit;
    }

    /**
     * Genera y descarga el Diploma en PDF para la boleta indicada.
     * URL de ejemplo: /expoescom/pdf/diploma/2020123456
     */
    public function diploma(string $boleta)
    {
        // 1) Traer datos y verificar que sea ganador
        $stmt = $this->pdo->prepare("
            SELECT 
              a.boleta,
              a.nombre,
              e.es_ganador
            FROM alumnos a
            JOIN miembros_equipo me ON me.alumno_boleta = a.boleta
            JOIN equipos e         ON e.id            = me.equipo_id
            WHERE a.boleta = ?
        ");
        $stmt->execute([$boleta]);
        $info = $stmt->fetch();

        if (!$info || !$info['es_ganador']) {
            die("No estás marcado como ganador.");
        }

        // 2) Crear el PDF
        $pdf = new \PDFCabecera();
        $pdf->AddPage();

        // Título Diploma
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 12, "Diploma ExpoESCOM 2025", 0, 1, 'C');
        $pdf->Ln(10);

        // Nombre del ganador
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, 10, "Otorgado a: " . utf8_decode($info['nombre']), 0, 1, 'C');
        $pdf->Ln(8);

        // Texto de felicitación
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->MultiCell(
            0,
            8,
            utf8_decode("Por su destacada participación y por haber sido seleccionado "
                . "como ganador en ExpoESCOM 2025. ¡Felicitaciones!"),
            0,
            'C'
        );
        $pdf->Ln(15);

        // Firma y fecha
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 8, "Fecha: " . date('d/m/Y'), 0, 1, 'R');
        $pdf->Ln(20);
        $pdf->Cell(0, 8, "________________________", 0, 1, 'R');
        $pdf->Cell(0, 8, "Coordinador ExpoESCOM", 0, 1, 'R');

        // 3) Descargar
        $pdf->Output('D', "Diploma_{$boleta}.pdf");
        exit;
    }
}
