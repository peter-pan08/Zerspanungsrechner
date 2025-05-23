<?php
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="zerspanung_export.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Material', 'Platte', 'vc (m/min)', 'f (mm/U)', 'ap (mm)', 'D (mm)', 'n (U/min)', 'Vf (mm/min)', 'Pc (kW)', 'Md (Nm)']);

session_start();
$data = $_SESSION['export'] ?? [];

if (!empty($data)) {
  fputcsv($output, [
    $data['material'] ?? '',
    $data['platte'] ?? '',
    $data['vc'] ?? '',
    $data['f'] ?? '',
    $data['ap'] ?? '',
    $data['D'] ?? '',
    $data['n'] ?? '',
    $data['vf'] ?? '',
    $data['pc'] ?? '',
    $data['md'] ?? ''
  ]);
} else {
  fputcsv($output, ['Keine Daten']);
}

fclose($output);
exit;
?>
