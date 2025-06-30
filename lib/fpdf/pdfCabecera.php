<?php

class PDFCabecera extends FPDF
{
    function Header()
    {
        // Fondo de página personalizado
        $this->Image(__DIR__ . '/../assets/images/fondo_acuse.png', 0, 0, 210, 297); // A4 full
    }

    function Footer()
    {
        // Puedes agregar pie de página si gustas
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('ExpoESCOM 2025 · escom.ipn.mx'), 0, 0, 'C');
    }
}
