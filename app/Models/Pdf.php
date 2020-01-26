<?php

namespace App\Models;

use Codedge\Fpdf\Fpdf\Fpdf;

class Pdf extends Fpdf
{
    public function Header()
    {
        global $title;
        global $reportName;
        global $headers;

        $this->Image('vendor/adminlte/dist/img/logo.png', 50, 9, 45);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->SetFont('arial', 'B', 17);
        $this->Cell(500, 35, utf8_decode("BeneTelecom,  $this->reportName"), 0, 0, 'L');
        $this->SetFont('arial', '', 10);
        $this->Cell(55, 35, utf8_decode("Emissão:"), 0, 0, 'L');
        $this->Cell(0, 35, utf8_decode($this->title), 0, 1, 'L');
        $this->Ln(5);
        
    }

    public function Footer()
    {
        $this->SetY(-29);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }
}