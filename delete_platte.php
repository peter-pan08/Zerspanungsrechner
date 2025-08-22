<?php
require 'session_check.php';
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit;
}

if (defined('DEMO_MODE') && DEMO_MODE) {
  echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
  exit;
}

$pdo = getPDO();

if (isset($_POST['id'])) {
  $stmt = $pdo->prepare("DELETE FROM platten WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  echo "OK";
}
?>
