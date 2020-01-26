<?php

namespace App\Models\Reports;

use Codedge\Fpdf\Fpdf\Fpdf;

class ByExtension extends Fpdf
{
    public function Header()
    {

        global $rTitle, $rName, $rUser, $rGroup, $rStart, $rExten, $rDepto, $rEnd; $rPrint;

        $this->Image('vendor/adminlte/dist/img/logo.png', 50, 9, 45);
        $this->SetFont('Arial', 'B', 15);
        $this->Cell(80);
        $this->SetFont('arial', 'B', 17);
        $this->Cell(500, 35, utf8_decode("BeneTelecom,  $this->rName"), 0, 0, 'L');
        $this->SetFont('arial', '', 10);
        $this->Cell(55, 35, utf8_decode("Emissão:"), 0, 0, 'L');
        $this->Cell(0, 35, utf8_decode($this->rTitle), 0, 1, 'L');
        $this->Ln(5);
        
        //Usuario do ramal
        $this->SetFont('arial', 'B', 11);
        $this->Cell(55, 20, utf8_decode("Usuário:"), 0, 0, 'L');
        $this->setFont('arial', '', 11);
        $this->Cell(300, 20, utf8_decode($this->rUser), 0, 0, 'L');
        
        //Grupo ao que o ramal pertence
        $this->SetFont('arial', 'B', 11);
        $this->Cell(45, 20, utf8_decode("Grupo:"), 0, 0, 'L');
        $this->setFont('arial', '', 11);
        $this->Cell(245, 20, utf8_decode($this->rGroup), 0, 0, 'L');
        
        //periodo inicial do relatorio
        $this->SetFont('arial', 'B', 11);
        $this->Cell(35, 20, utf8_decode("De:"), 0, 0, 'L');
        $this->setFont('arial', '', 11);
        $this->Cell(0, 20, utf8_decode(date('d/m/Y H:i:s', strtotime($this->rStart))), 0, 1, 'L');
        
        //Ramal do relatorio
        $this->SetFont('arial', 'B', 11);
        $this->Cell(55, 20, utf8_decode("Ramal:"), 0, 0, 'L');
        $this->setFont('arial', '', 11);
        $this->Cell(300, 20, utf8_decode($this->rExten), 0, 0, 'L');
        
        //Departamento do ramal
        $this->SetFont('arial', 'B', 11);
        $this->Cell(45, 20, utf8_decode("Depto:"), 0, 0, 'L');
        $this->setFont('arial', '', 11);
        $this->Cell(245, 20, utf8_decode($this->rDepto), 0, 0, 'L');
        
        //periodo final do relatorio
        $this->SetFont('arial', 'B', 11);
        $this->Cell(35, 20, utf8_decode("Até:"), 0, 0, 'L');
        $this->setFont('arial', '', 11);
        $this->Cell(0, 20, utf8_decode(date('d/m/Y H:i:s', strtotime($this->rEnd))), 0, 1, 'L');
        
        //cabeçalho tabela do relatorio
        $this->ln(9);
        if($this->rPrint == true):
            $this->Cell(0, 1, "", "B", 1, 'C');
            $this->SetFont('arial', 'B', 11);
            $this->SetFillColor(160, 160, 160);
            $this->Cell(5, 20, utf8_decode(''), 'T,B', 0, 'C', true);
            //$this->Cell(50, 20, utf8_decode(""), 'T,B', 0, 'C', true);
            $this->Cell(70, 20, utf8_decode('Data / Hora'), 'T,B', 0, 'C', true);
            $this->Cell(60, 20, utf8_decode("Direção"), 'T,B', 0, 'C', true);
            $this->Cell(50, 20, utf8_decode("Tronco"), 'T,B', 0, 'C', true);
            $this->Cell(110, 20, utf8_decode("Numero discado"), 'T,B', 0, 'C', true);
            $this->Cell(160, 20, utf8_decode("Localidade"), 'T,B', 0, 'C', true);
            $this->Cell(160, 20, utf8_decode("Agenda"), 'T,B', 0, 'C', true);
            $this->Cell(70, 20, utf8_decode("Serviço"), 'T,B', 0, 'C', true);
            $this->Cell(50, 20, utf8_decode("Duração"), 'T,B', 0, 'C', true);
            $this->Cell(50, 20, utf8_decode("Valor"), 'T,B', 1, 'C', true);
            $this->SetFont('arial', '', 10);
        endif;
    }

    public function Footer()
    {
        $this->SetY(-29);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . ' / {nb}', 0, 0, 'C');
    }
}