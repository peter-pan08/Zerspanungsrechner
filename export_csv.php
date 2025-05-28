<?php
// CSV-Export für Zerspanungsdaten
session_start();

// CSV-Header senden
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="zerspanung_export.csv"');

$output = fopen('php://output', 'w');
// Spaltenüberschriften
fputcsv($output, [
  'Material',
  'Schneidplatte',
  'vc (m/min)',
  'f (mm/U)',
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

// Wenn Daten da sind, in die CSV schreiben, sonst Fehlermeldung
if (!empty($data)) {
  fputcsv($output, [
    $data['material']   ?? '',
    $data['platte']     ?? '',
    $data['vc']         ?? '',
    $data['f']          ?? '',
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
