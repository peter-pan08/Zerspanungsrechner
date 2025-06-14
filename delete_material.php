<?php
require 'session_check.php';
require 'require_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  exit;
}

if (defined('DEMO_MODE') && DEMO_MODE) {
  echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
  exit;
}

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['id'])) {
  $stmt = $pdo->prepare("DELETE FROM materialien WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  echo "OK";
}
?>
