<?php
require 'session_check.php';
require 'require_config.php';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (isset($_POST['loeschen'])) {
  if (defined('DEMO_MODE') && DEMO_MODE) {
    echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
    exit;
  }
  $stmt = $pdo->prepare("DELETE FROM fraeser WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  exit;
}

$durchmesser = $_POST['durchmesser'] ?? null;
if ($durchmesser !== null && (!is_numeric($durchmesser) || $durchmesser <= 0)) {
  http_response_code(400);
  echo 'Ungültiger Durchmesser';
  exit;
}

// INSERT oder UPDATE
if (!empty($_POST['id'])) {
  // UPDATE
  $stmt = $pdo->prepare("UPDATE fraeser SET name=?, typ=?, durchmesser=?, zaehne=?, gruppen=?, vc=?, fz=? WHERE id=?");
  $stmt->execute([
    $_POST['name'],
    $_POST['typ'],
    $_POST['durchmesser'],
    $_POST['zaehne'],
    $_POST['gruppen'],
    $_POST['vc'],
    $_POST['fz'],
    $_POST['id']
  ]);
} else {
  // INSERT
  $stmt = $pdo->prepare("INSERT INTO fraeser (name, typ, durchmesser, zaehne, gruppen, vc, fz) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    $_POST['name'],
    $_POST['typ'],
    $_POST['durchmesser'],
    $_POST['zaehne'],
    $_POST['gruppen'],
    $_POST['vc'],
    $_POST['fz']
  ]);
}
echo "OK";
?>
