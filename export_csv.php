<?php
// CSV-Export für Zerspanungsdaten
session_start();

// CSV-Header senden
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="zerspanung_export.csv"');

$output = fopen('php://output', 'w');
// Spaltenüberschriften
$feedLabel = 'f oder fz';
if (isset($_SESSION['export']['fz'])) {
    $feedLabel = 'fz (mm/Zahn)';
} elseif (isset($_SESSION['export']['f'])) {
    $feedLabel = 'f (mm/U)';
} elseif (isset($_SESSION['export']['vf'])) {
    $feedLabel = 'vf (mm/min)';
}
fputcsv($output, [
  'Material',
  'Werkzeug',
  'vc (m/min)',
  $feedLabel,
  'ap (mm)',
  'Durchmesser (mm)',
  'Spindeldrehzahl (U/min)',
  'Motordrehzahl (U/min)',
  'Untersetzung',
  'Getriebewirkungsgrad',
  'Vorschubgeschwindigkeit (mm/min)',
  'Leistungsaufnahme (kW)',
  'Motorlast (W)',
  'Schnittkraft (N)',
  'Drehmoment (Spindel, Nm)',
  'Drehmoment (Motor, Nm)',
  'Motordrehmoment (Nm)',
  'Motordrehmoment-Auslastung (%)'
]);

// Daten aus Session holen (Session-Schlüssel "export")
$data = $_SESSION['export'] ?? [];

if (!isset($data['fraeser']) && isset($data['platte'])) {
    $data['fraeser'] = $data['platte'];
}
if (!isset($data['fz']) && isset($data['f'])) {
    $data['fz'] = $data['f'];
}
$feedValue = $data['fz'] ?? ($data['f'] ?? ($data['vf'] ?? ''));

// Wenn Daten da sind, in die CSV schreiben, sonst Fehlermeldung
if (!empty($data)) {
  fputcsv($output, [
    $data['material']   ?? '',
    $data['fraeser']    ?? ($data['platte'] ?? ''),
    $data['vc']         ?? '',
    $feedValue,
    $data['ap']         ?? '',
    $data['D']          ?? '',
    $data['n']          ?? '',
    $data['nMot']       ?? '',
    $data['untersetzung'] ?? '',
    $data['wirkungsgrad'] ?? '',
    $data['vf']         ?? '',
    $data['pc']         ?? '',
    $data['motorLast']  ?? '',
    $data['Fc']         ?? '',
    $data['md_spindel'] ?? '',
    $data['md_motor']   ?? '',
    $data['motordrehmoment'] ?? '',
    $data['drehmomentMotorProzent'] ?? ''
  ]);
} else {
  fputcsv($output, ['⚠️ Keine Exportdaten gefunden.']);
}

fclose($output);
exit;
?>
