<?php
require 'session_check.php';
require_once 'db.php';

$pdo = getPDO();

if (isset($_POST['loeschen'])) {
  if (defined('DEMO_MODE') && DEMO_MODE) {
    echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
    exit;
  }
  $stmt = $pdo->prepare("DELETE FROM materialien WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  exit;
}

// INSERT oder UPDATE
if (!empty($_POST['id'])) {
  // UPDATE
  $stmt = $pdo->prepare("UPDATE materialien SET name=?, typ=?, gruppe=?, vc_hss=?, vc_hartmetall=?, kc=? WHERE id=?");
  $stmt->execute([
    $_POST['name'],
    $_POST['typ'],
    $_POST['gruppe'],
    $_POST['vc_hss'],
    $_POST['vc_hartmetall'],
    $_POST['kc'],
    $_POST['id']
  ]);
} else {
  // INSERT
  $stmt = $pdo->prepare("INSERT INTO materialien (name, typ, gruppe, vc_hss, vc_hartmetall, kc) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    $_POST['name'],
    $_POST['typ'],
    $_POST['gruppe'],
    $_POST['vc_hss'],
    $_POST['vc_hartmetall'],
    $_POST['kc']
  ]);
}
echo "OK";
?>
