<?php
class PDFCabecera extends FPDF
{
    function Header()
    {
        // Obtener orientación actual
        $orientation = $this->CurOrientation; // 'P' o 'L'

        // Definir ruta y tamaño de imagen de fondo
        if ($orientation === 'P') {
            // Acuse (vertical)
            $background = __DIR__ . '/../../assets/images/formato-vertical.png';
            $this->Image($background, 0, 0, 210, 297); // A4 vertical: 210x297 mm
        } else {
            // Diploma (horizontal)
            $background = __DIR__ . '/../../assets/images/formato-horizontal.png';
            $this->Image($background, 0, 0, 297, 210); // A4 horizontal: 297x210 mm
        }

        // Márgenes por defecto para el contenido (puedes ajustar)
        $this->SetMargins(20, 40, 20);
        $this->SetAutoPageBreak(true, 30);
    }

    function Footer()
    {
        // Puedes agregar pie si lo deseas
        // $this->SetY(-15);
        // $this->SetFont('Arial', 'I', 8);
        // $this->Cell(0, 10, 'ExpoESCOM 2025', 0, 0, 'C');
    }
}
