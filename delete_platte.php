<?php
require 'session_check.php';
require 'config.php';

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
