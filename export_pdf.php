<?php
// export_pdf.php
session_start();

// Lade TCPDF (Composer autoload)
require_once __DIR__ . '/vendor/autoload.php';

// Daten aus der Session
$data = $_SESSION['export'] ?? [];

// Neues PDF erzeugen
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Zerspanungsrechner');
$pdf->SetAuthor('Dein Name');
$pdf->SetTitle('Zerspanungs-Ergebnis');
$pdf->SetMargins(15, 30, 15);
$pdf->SetAutoPageBreak(true, 20);

// Neue Seite
$pdf->AddPage();

// Logo einbinden (SVG oder PNG)
$logoFile = __DIR__ . '/dryba_logo_100.svg';
if (file_exists($logoFile)) {
    // SVG-Logo
    if (method_exists($pdf, 'ImageSVG')) {
        $pdf->ImageSVG(
            $file = $logoFile,
            $x = 15,
            $y = 10,
            $w = 50,
            $h = '',
            $link = '',
            $align = '',
            $palign = '',
            $border = 0,
            $fitonpage = true
        );
    } else {
        // PNG-Fallback (falls du eine PNG stattdessen nutzt)
        $pngFallback = __DIR__ . '/dryba_logo_100.png';
        if (file_exists($pngFallback)) {
            $pdf->Image($pngFallback, 15, 10, 50, '', 'PNG');
        }
    }
}

// Überschrift
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Ln(15);
$pdf->Cell(0, 0, 'Zerspanungs-Ergebnis', 0, 1, 'C');
$pdf->Ln(5);

// Report-Daten ausgeben
$pdf->SetFont('helvetica', '', 12);
$html = '';
if (!empty($data)) {
    $html .= '<table cellpadding="4">';
    foreach ([
        'Material'               => $data['material'] ?? '',
        'Schneidplatte'          => $data['platte'] ?? '',
        'vc (m/min)'             => $data['vc'] ?? '',
        'f (mm/U)'               => $data['f'] ?? '',
        'ap (mm)'                => $data['ap'] ?? '',
        'Durchmesser (mm)'       => $data['D'] ?? '',
        'Drehzahl (U/min)'       => $data['n'] ?? '',
        'Vorschubgeschwindigkeit' => $data['vf'] ?? '',
        'Leistungsaufnahme (kW)' => $data['pc'] ?? '',
        'Motorlast (W)'          => $data['motorLast'] ?? '',
        'Schnittkraft (N)'       => $data['Fc'] ?? '',
        'Drehmoment (Nm)'        => $data['md'] ?? ''
    ] as $label => $value) {
        $html .= '<tr><td width="40%"><strong>' . $label . '</strong></td><td>' . $value . '</td></tr>';
    }
    $html .= '</table>';
} else {
    $html = '<p>⚠️ Keine Daten in der Sitzung gefunden.</p>';
}

$pdf->writeHTML($html, true, false, true, false, '');

// Ausgabe
$pdf->Output('zerspanungsergebnis.pdf', 'I');
exit;
