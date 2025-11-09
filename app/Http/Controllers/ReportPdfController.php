<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FPDF;

/**
 * Custom FPDF Class para sa Header at Footer
 */
class ReportPDF extends FPDF
{
    protected $reportTitle = 'REPORT';

    function SetReportTitle($title)
    {
        $this->reportTitle = mb_strtoupper($title);
    }

    // Page header
    function Header()
{
    // 🏠 Add Logo (left side of title)
    $this->Image(public_path('uploads/ddf63450-50d1-4fd2-9994-7a08dd496ac1-removebg-preview.png'), 70, 11, 7); 
    // (x=25, y=10, width=18mm; adjust width if gusto mo mas malaki o mas maliit)

    // 🔹 Title beside logo
    $this->SetFont('Arial', 'B', 16);
    $this->Cell(0, 10, 'Subdirent Subdivision', 0, 1, 'C');
    
    $this->SetFont('Arial', '', 10);
    $this->Cell(0, 5, 'Official House Rental Report', 0, 1, 'C');
    
    $this->SetFont('Arial', 'B', 12);
    $this->Cell(0, 10, $this->reportTitle, 0, 1, 'C');
    
    $this->SetFont('Arial', '', 10);
    $this->Cell(0, 5, 'Date Generated: ' . date('M d, Y'), 0, 1, 'C');
    
    $this->Ln(10);
}

    // Page footer
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // BAGO: Mas advanced na table function para ayusin ang alignment
    function BasicTable($headers, $data)
    {
        // Kalkulahin ang lapad ng bawat column
        $numHeaders = count($headers);
        if ($numHeaders == 0) return;
        $width = 195 / $numHeaders; // ~195mm usable width

        // Header
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(230, 230, 230); // Light grey
        foreach ($headers as $header) {
            $this->Cell($width, 7, $header, 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Data
        $this->SetFont('Arial', '', 8);
        $this->SetFillColor(255); // White background

        foreach ($data as $row) {
            // Kunin ang current Y position bago magsimula ng row
            $startY = $this->GetY();
            $startX = $this->GetX();
            
            $maxHeight = 0; // Para i-track ang pinakamataas na cell
            
            // Unang pass: Kalkulahin ang kailangang height (6mm per line)
            for ($i = 0; $i < $numHeaders; $i++) {
                $cellData = $row[$i] ?? 'N/A';
                $lines = $this->NbLines($width, $cellData);
                $height = 6 * $lines;
                if ($height > $maxHeight) {
                    $maxHeight = $height;
                }
            }

            // Pangalawang pass: Iguhit ang mga cells
            $this->SetX($startX); // Balik sa simula ng row
            
            for ($i = 0; $i < $numHeaders; $i++) {
                $cellData = $row[$i] ?? 'N/A';
                $xPos = $this->GetX();
                $yPos = $this->GetY();
                
                // Iguhit ang box (Rect)
                $this->Rect($xPos, $yPos, $width, $maxHeight);
                
                // Ilagay ang text sa loob ng box (MultiCell)
                // Ang 0 border ay para hindi mag-doble ang linya
                $this->MultiCell($width, 6, $cellData, 0, 'C'); 
                
                // Ilipat ang cursor sa tamang position para sa susunod na cell
                $this->SetXY($xPos + $width, $yPos); 
            }
            
            // Ilipat ang cursor sa ilalim ng row na katatapos lang
            $this->SetY($startY + $maxHeight);
        }
    }

    // Utility function para bilangin ang lines (kailangan ito)
    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0)
            $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l += $cw[$c] ?? 0; // Nagdagdag ng fallback para sa unknown characters
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                } else
                    $i = $sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else
                $i++;
        }
        return $nl;
    }
}


/**
 * Main Controller
 */
class ReportPdfController extends Controller
{
    public function generatePdf(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'headers' => 'required|array',
            'data' => 'required|array',
        ]);

        $title = $request->input('title');
        $headers = $request->input('headers');
        $data = $request->input('data');

        $pdf = new ReportPDF('P', 'mm', 'Letter');
        $pdf->SetReportTitle($title);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->BasicTable($headers, $data);
        
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="report.pdf"');
    }
}