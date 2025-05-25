<?php
session_start();

// Prüfen, ob wir Export-Daten in der Session haben
if (empty($_SESSION['export_data'])) {
    http_response_code(400);
    echo "Keine Export-Daten gefunden.";
    exit;
}

// CSV-Header senden
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="zerspanung.csv"');

// Ausgabe-Stream öffnen
$out = fopen('php://output', 'w');

// Spaltenüberschriften
fputcsv($out, [
    'Material',
    'Schneidplatte',
    'vc (m/min)',
    'f (mm/U)',
    'ap (mm)',
    'Durchmesser (mm)',
    'n Spindel (1/min)',
    'n Motor (1/min)',
    'vf (mm/min)',
    'Motorlast (W)',
    'Drehmoment (Nm)'
]);

// Daten aus Session
$data = $_SESSION['export_data'];
fputcsv($out, [
    $data['material'],
    $data['platte'],
    $data['vc'],
    $data['f'],
    $data['ap'],
    $data['D'],
    $data['n'],
    $data['nMot'],
    $data['vf'],
    $data['pc'],
    $data['md']
]);

fclose($out);

// Optional: Session-Daten löschen, damit beim nächsten Aufruf nicht noch einmal exportiert wird
unset($_SESSION['export_data']);
