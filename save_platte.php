<?php
require 'session_check.php';
require_once 'db.php';

$pdo = getPDO();

if (isset($_POST['loeschen'])) {
  if (defined('DEMO_MODE') && DEMO_MODE) {
    echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
    exit;
  }
  $stmt = $pdo->prepare("DELETE FROM platten WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  exit;
}

// INSERT oder UPDATE
if (!empty($_POST['id'])) {
  // UPDATE
  $stmt = $pdo->prepare("UPDATE platten SET name=?, typ=?, gruppen=?, vc=? WHERE id=?");
  $stmt->execute([
    $_POST['name'],
    $_POST['typ'],
    $_POST['gruppen'],
    $_POST['vc'],
    $_POST['id']
  ]);
} else {
  // INSERT
  $stmt = $pdo->prepare("INSERT INTO platten (name, typ, gruppen, vc) VALUES (?, ?, ?, ?)");
  $stmt->execute([
    $_POST['name'],
    $_POST['typ'],
    $_POST['gruppen'],
    $_POST['vc']
  ]);
}
echo "OK";
?>
