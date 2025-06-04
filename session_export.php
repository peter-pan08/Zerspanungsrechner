<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (is_array($data)) {
  // vorhandene Exportdaten beibehalten und um neue Werte ergaenzen
  $_SESSION['export'] = array_merge($_SESSION['export'] ?? [], $data);
  // Vorsorglich vf separat speichern, falls dieser Feed-Modus verwendet wird
  if (isset($data['vf'])) {
    $_SESSION['export']['vf'] = $data['vf'];
  }
  http_response_code(200);
  echo json_encode(["status" => "ok"]);
} else {
  http_response_code(400);
  echo json_encode(["error" => "Ungültige Daten"]);
}
?>
