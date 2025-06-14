<?php
require 'session_check.php';
require 'require_config.php';

if (defined('DEMO_MODE') && DEMO_MODE) {
  echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
  exit;
}

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_POST['id'] ?? $_GET['id'] ?? null;
if ($id) {
  $stmt = $pdo->prepare("DELETE FROM fraeser WHERE id = ?");
  $stmt->execute([$id]);
  echo "OK";
}
?>
