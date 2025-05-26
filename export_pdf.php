<?php
// export_pdf.php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

// Hole Export-Daten aus der Session
$data = $_SESSION['export'] ?? [];

// Neues PDF erzeugen
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Zerspanungsrechner');
$pdf->SetAuthor('Zerspanungsrechner');
$pdf->SetTitle('Zerspanungs-Ergebnis');
$pdf->SetMargins(15, 30, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->addPage();

// Logo einbinden: PNG bevorzugt, sonst SVG
$logoPng = __DIR__ . '/dryba_logo_100.png';
$logoSvg = __DIR__ . '/dryba_logo_100.svg';
if (file_exists($logoPng)) {
    // PNG-Logo bei 40 mm Breite
    $pdf->Image($logoPng, 15, 10, 40, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
} elseif (file_exists($logoSvg) && method_exists($pdf, 'ImageSVG')) {
    // SVG-Logo fallback
    $pdf->ImageSVG(
        $file = $logoSvg,
        $x = 15,
        $y = 10,
        $w = 40,
        $h = 0,
        $link = '',
        $align = '',
        $palign = '',
        $border = 0,
        $fitonpage = true
    );
}

// Abstand nach Logo
$pdf->Ln(20);

// Überschrift
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 0, 'Zerspanungs-Ergebnis', 0, 1, 'C');
$pdf->Ln(5);

// Daten-Tabelle
$pdf->SetFont('helvetica', '', 12);
$html = '<table cellpadding="4">';
$labels = [
    'Material'               => $data['material'] ?? '-',
    'Schneidplatte'          => $data['platte'] ?? '-',
    'vc (m/min)'             => $data['vc'] ?? '-',
    'f (mm/U)'               => $data['f'] ?? '-',
    'ap (mm)'                => $data['ap'] ?? '-',
    'Durchmesser (mm)'       => $data['D'] ?? '-',
    'Drehzahl (U/min)'       => $data['n'] ?? '-',
    'Vorschubgeschwindigkeit' => $data['vf'] ?? '-',
    'Leistungsaufnahme (kW)' => $data['pc'] ?? '-',
    'Motorlast (W)'          => $data['motorLast'] ?? '-',
    'Schnittkraft (N)'       => $data['Fc'] ?? '-',
    'Drehmoment (Nm)'        => $data['md'] ?? ''
];
foreach ($labels as $label => $value) {
    $html .= '<tr>'
           . '<td width="40%"><strong>' . $label . '</strong></td>'
           . '<td>' . $value . '</td>'
           . '</tr>';
}
$html .= '</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Ausgabe im Browser
$pdf->Output('zerspanungsergebnis.pdf', 'I');
exit;
