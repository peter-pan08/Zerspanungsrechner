<?php
require 'vendor/autoload.php';
session_start();
$data = $_SESSION['export'] ?? [];

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Zerspanung');

$sheet->setCellValue('A1', 'Bezeichnung');
$sheet->setCellValue('B1', 'Wert');

$feedLabel = 'f oder fz';
$feedKey = 'fz';
if (isset($data['fz'])) {
    $feedLabel = 'fz (mm/Zahn)';
    $feedKey = 'fz';
} elseif (isset($data['f'])) {
    $feedLabel = 'f (mm/U)';
    $feedKey = 'f';
} elseif (isset($data['vf'])) {
    $feedLabel = 'vf (mm/min)';
    $feedKey = 'vf';
}
$labels = [
  'Material' => 'material',
  'Werkzeug' => 'fraeser',
  'vc (m/min)' => 'vc',
  $feedLabel => $feedKey,
  'ap (mm)' => 'ap',
  'Durchmesser (mm)' => 'D',
  'Spindeldrehzahl (U/min)' => 'n',
  'Motordrehzahl (U/min)' => 'nMot',
  'Untersetzung' => 'untersetzung',
  'Getriebewirkungsgrad' => 'wirkungsgrad',
  'Vorschubgeschwindigkeit (mm/min)' => 'vf',
  'Leistungsaufnahme (kW)' => 'pc',
  'Motorlast (W)' => 'motorLast',
  'Schnittkraft (N)' => 'Fc',
  'Drehmoment (Spindel, Nm)' => 'md_spindel',
  'Drehmoment (Motor, Nm)' => 'md_motor',
  'Motordrehmoment (Nm)' => 'motordrehmoment',
  'Motordrehmoment-Auslastung (%)' => 'drehmomentMotorProzent'
];

if (!isset($data['fraeser']) && isset($data['platte'])) {
    $data['fraeser'] = $data['platte'];
}
if (!isset($data['fz']) && isset($data['f'])) {
    $data['fz'] = $data['f'];
}

$row = 2;
if (!empty($data)) {
  foreach ($labels as $label => $key) {
    $sheet->setCellValue('A' . $row, $label);
    $sheet->setCellValue('B' . $row, $data[$key] ?? '');
    $row++;
  }
} else {
  $sheet->setCellValue('A2', 'Keine Daten gefunden');
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="zerspanung_export.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
