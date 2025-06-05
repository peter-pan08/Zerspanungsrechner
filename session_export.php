<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (is_array($data)) {
  // vorhandene Exportdaten beibehalten und um neue Werte ergaenzen
  $_SESSION['export'] = array_merge($_SESSION['export'] ?? [], $data);
  // Vorsorglich alle Feed-Werte separat speichern
  foreach (['fz', 'f', 'vf'] as $key) {
    if (isset($data[$key])) {
      $_SESSION['export'][$key] = $data[$key];
    }
  }
  http_response_code(200);
  echo json_encode(["status" => "ok"]);
} else {
  http_response_code(400);
  echo json_encode(["error" => "Ungültige Daten"]);
}
?>
