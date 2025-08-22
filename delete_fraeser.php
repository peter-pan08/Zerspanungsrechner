<?php
require 'session_check.php';
require_once 'db.php';

if (defined('DEMO_MODE') && DEMO_MODE) {
  echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
  exit;
}

$pdo = getPDO();

$id = $_POST['id'] ?? $_GET['id'] ?? null;
if ($id) {
  $stmt = $pdo->prepare("DELETE FROM fraeser WHERE id = ?");
  $stmt->execute([$id]);
  echo "OK";
}
?>
