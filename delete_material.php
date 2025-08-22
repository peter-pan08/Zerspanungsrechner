<?php
require 'session_check.php';
require_once 'csrf.php';
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

if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
  http_response_code(400);
  echo 'Ungültiger CSRF-Token';
  exit;
}

if (isset($_POST['id'])) {
  $stmt = $pdo->prepare("DELETE FROM materialien WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  echo "OK";
}
?>
