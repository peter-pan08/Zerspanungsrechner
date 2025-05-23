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

$row = 2;
if (!empty($data)) {
  foreach ($data as $key => $val) {
    $sheet->setCellValue('A' . $row, $key);
    $sheet->setCellValue('B' . $row, $val);
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
