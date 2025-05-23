<?php
session_start();

$data = json_decode(file_get_contents("php://input"), true);

if (is_array($data)) {
  $_SESSION['export'] = $data;
  http_response_code(200);
  echo json_encode(["status" => "ok"]);
} else {
  http_response_code(400);
  echo json_encode(["error" => "Ungültige Daten"]);
}
?>
