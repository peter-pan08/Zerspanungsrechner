<?php
// export_pdf.php
session_start();

require_once __DIR__ . '/vendor/autoload.php';

// Export-Daten aus der Session
$data = $_SESSION['export'] ?? [];
if (!isset($data['fraeser']) && isset($data['platte'])) {
    $data['fraeser'] = $data['platte'];
}
if (!isset($data['fz']) && isset($data['f'])) {
    $data['fz'] = $data['f'];
}

// Neues PDF erzeugen
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetCreator('Zerspanungsrechner');
$pdf->SetAuthor('Zerspanungsrechner');
$pdf->SetTitle('Zerspanungs-Ergebnis');
$pdf->SetMargins(15, 30, 15);
$pdf->SetAutoPageBreak(true, 20);
$pdf->addPage();

// Logo einbinden: SVG bevorzugt, sonst PNG
$logoSvg = __DIR__ . '/dryba_logo_100.svg';
$logoPng = __DIR__ . '/dryba_logo_100.png';
if (file_exists($logoSvg) && method_exists($pdf, 'ImageSVG')) {
    // SVG einbinden
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
} elseif (file_exists($logoPng)) {
    // PNG-Fallback
    $pdf->Image(
        $logoPng,
        $x = 15,
        $y = 10,
        $w = 40,
        $h = 0,
        $type = 'PNG',
        $link = '',
        $align = 'T',
        $resize = true,
        $dpi = 300
    );
}

// Abstand nach Logo
$pdf->Ln(20);

// Überschrift\ n$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 0, 'Zerspanungs-Ergebnis', 0, 1, 'C');
$pdf->Ln(5);

// Tabelle mit Daten
$pdf->SetFont('helvetica', '', 12);
$html = '<table cellpadding="4">';
$labels = [
    'Material'                 => $data['material'] ?? '-',
    'Werkzeug'                 => $data['fraeser'] ?? ($data['platte'] ?? '-'),
    'vc (m/min)'               => $data['vc'] ?? '-',
    'f oder fz'                => $data['fz'] ?? ($data['f'] ?? '-'),
    'ap (mm)'                  => $data['ap'] ?? '-',
    'Durchmesser (mm)'         => $data['D'] ?? '-',
    'Spindeldrehzahl'          => isset($data['n']) ? $data['n'] . ' U/min' : '-',
    'Motordrehzahl'            => (isset($data['nMot']) && isset($data['untersetzung']))
                                   ? $data['nMot'] . ' U/min (Untersetzung ' . $data['untersetzung'] . ')'
                                   : (isset($data['nMot']) ? $data['nMot'] . ' U/min' : '-'),
    'Getriebewirkungsgrad'     => isset($data['wirkungsgrad']) ? $data['wirkungsgrad'] : '-',
    'Vorschubgeschwindigkeit'  => isset($data['vf']) ? $data['vf'] . ' mm/min' : '-',
    'Leistungsaufnahme (kW)'   => $data['pc'] ?? '-',
    'Motorlast (W)'            => $data['motorLast'] ?? '-',
    'Schnittkraft (N)'         => $data['Fc'] ?? '-',
    'Drehmoment (Spindel, Nm)' => $data['md_spindel'] ?? '-',
    'Drehmoment (Motor, Nm)'   => $data['md_motor'] ?? '-',
    'Motordrehmoment (Nm)'        => $data['motordrehmoment'] ?? '-',
    'Motordrehmoment-Auslastung'  => isset($data['drehmomentMotorProzent']) && isset($data['motordrehmoment'])
                                  ? $data['drehmomentMotorProzent'] . '% von ' . $data['motordrehmoment'] . ' Nm'
                                  : '-'
];
foreach ($labels as $label => $value) {
    $html .= '<tr><td width="40%"><strong>' . $label . '</strong></td><td>' . $value . '</td></tr>';
}
$html .= '</table>';
$pdf->writeHTML($html, true, false, true, false, '');

// PDF-Ausgabe im Browser
$pdf->Output('zerspanungsergebnis.pdf', 'I');
exit;
