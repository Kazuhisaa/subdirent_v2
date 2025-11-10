<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// 1. INAYOS KO: Ito 'yung tamang path galing sa root ng project mo
require_once base_path('fpdf186/fpdf.php');

/**
 * Custom FPDF Class para sa Header at Footer
 * 2. INAYOS KO: Nilagyan ko ng '\' sa unahan para mahanap 'yung class
 */
class ReportPDF extends \FPDF
{
    protected $reportTitle = 'REPORT';

    function SetReportTitle($title)
    {
        $this->reportTitle = mb_strtoupper($title);
    }

    // Page header
    function Header()
    {
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

    function BasicTable($headers, $data)
    {
        $numHeaders = count($headers);
        if ($numHeaders == 0) return;
        
        // Portrait ('P') 'Letter' size ay may usable width na mga 190mm
        $usableWidth = $this->GetPageWidth() - $this->lMargin - $this->rMargin; 
        $width = $usableWidth / $numHeaders;

        // Header
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(10, 37, 64); // Dark Blue
        $this->SetTextColor(255); // White
        foreach ($headers as $header) {
            $this->Cell($width, 7, $header, 1, 0, 'C', true);
        }
        $this->Ln();
        
        // Data
        $this->SetFont('Arial', '', 8);
        $this->SetTextColor(0); // Black
        $this->SetFillColor(255, 255, 255); // Default White

        if (empty($data)) {
            $this->Cell($usableWidth, 10, 'No data available for this report.', 1, 1, 'C');
            return;
        }
        
        $fill = false; 
        foreach ($data as $row) {
            // ✅ FIXED: Correct conditional fill color
            if ($fill) {
                $this->SetFillColor(240, 240, 240); // light gray
            } else {
                $this->SetFillColor(255, 255, 255); // white
            }

            $startY = $this->GetY();
            $startX = $this->GetX();
            $maxHeight = 6; 
            
            // 1st pass: Calculate max height
            for ($i = 0; $i < $numHeaders; $i++) {
                $cellData = $row[$i] ?? 'N/A';
                $cellData = html_entity_decode($cellData);
                $lines = $this->NbLines($width, $cellData);
                $height = 6 * $lines;
                if ($height > $maxHeight) {
                    $maxHeight = $height;
                }
            }

            // 2nd pass: Draw cells
            $this->SetX($startX); 
            
            for ($i = 0; $i < $numHeaders; $i++) {
                $cellData = $row[$i] ?? 'N/A';
                $cellData = html_entity_decode($cellData);
                $xPos = $this->GetX();
                $yPos = $this->GetY();
                
                // Draw box
                $this->Rect($xPos, $yPos, $width, $maxHeight, 'DF'); 
                
                // Draw text (Left-aligned)
                $this->MultiCell($width, 6, $cellData, 0, 'L'); 
                
                // Reset position for next cell
                $this->SetXY($xPos + $width, $yPos); 
            }
            
            // Move cursor below the tallest cell
            $this->SetY($startY + $maxHeight);
            $fill = !$fill;
        }
    }

    // Utility function (kailangan 'to)
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
            $l += $cw[$c] ?? 0; 
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
    // 3. INAYOS KO: 'generatePdf' to 'generate' para tumugma sa route
    public function generatePDF(Request $request) 
    {
        $request->validate([
            'title' => 'required|string',
            'headers' => 'required|array',
            'data' => 'required|array',
        ]);

        $title = $request->input('title');
        $headers = $request->input('headers');
        $data = $request->input('data');

        $pdf = new ReportPDF('P', 'mm', 'Letter'); // Portrait mode
        $pdf->SetReportTitle($title);
        $pdf->AliasNbPages();
        $pdf->AddPage();
        $pdf->BasicTable($headers, $data);
        
        // 'S' = return as string
        // 4. INAYOS KO: Ginawang 'inline' para sa JavaScript
        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="report.pdf"');
    }
}
