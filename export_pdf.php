<?php
require_once 'vendor/autoload.php';
session_start();
$data = $_SESSION['export'] ?? [];

use TCPDF;

$pdf = new TCPDF();
$pdf->SetCreator('Zerspanungsrechner');
$pdf->SetAuthor('Dein Rechner');
$pdf->SetTitle('Zerspanungs-Ergebnis');
$pdf->SetMargins(15, 20, 15);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Zerspanungsdaten', 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);

if (!empty($data)) {
  $tbl = '<table border="1" cellpadding="4">
    <tr style="background-color:#e0e0e0;"><th>Bezeichnung</th><th>Wert</th></tr>';

  foreach ($data as $key => $val) {
    $tbl .= '<tr><td>' . htmlspecialchars($key) . '</td><td>' . htmlspecialchars($val) . '</td></tr>';
  }

  $tbl .= '</table>';
  $pdf->writeHTML($tbl, true, false, false, false, '');
} else {
  $pdf->Write(0, '⚠️ Keine Daten in der Sitzung gefunden.', '', 0, 'L', true, 0, false, false, 0);
}

$pdf->Output('zerspanung_bericht.pdf', 'D');
?>
