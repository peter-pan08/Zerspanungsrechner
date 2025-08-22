<?php
require 'session_check.php';
require_once 'csrf.php';
require_once 'db.php';

$pdo = getPDO();

if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
  http_response_code(400);
  echo 'Ungültiger CSRF-Token';
  exit;
}

if (isset($_POST['loeschen'])) {
  if (defined('DEMO_MODE') && DEMO_MODE) {
    echo "🚫 Löschen im Demo-Modus nicht erlaubt.";
    exit;
  }
  $id = $_POST['id'] ?? null;
  if ($id === null || !ctype_digit((string)$id)) {
    http_response_code(400);
    echo 'Ungültige ID';
    exit;
  }
  $stmt = $pdo->prepare("DELETE FROM materialien WHERE id = ?");
  $stmt->execute([(int)$id]);
  exit;
}

// Eingaben validieren
$name = trim($_POST['name'] ?? '');
if ($name === '') {
  http_response_code(400);
  echo 'Ungültiger Name';
  exit;
}

$typ = trim($_POST['typ'] ?? '');
if ($typ === '') {
  http_response_code(400);
  echo 'Ungültiger Typ';
  exit;
}

$gruppe = $_POST['gruppe'] ?? '';
$allowedGruppen = ['P', 'M', 'K', 'N', 'S', 'H'];
if (!in_array($gruppe, $allowedGruppen, true)) {
  http_response_code(400);
  echo 'Ungültige Gruppe';
  exit;
}

$vc_hss = $_POST['vc_hss'] ?? null;
if ($vc_hss === null || !is_numeric($vc_hss) || $vc_hss <= 0) {
  http_response_code(400);
  echo 'Ungültiges vc HSS';
  exit;
}
$vc_hss = floatval($vc_hss);

$vc_hartmetall = $_POST['vc_hartmetall'] ?? null;
if ($vc_hartmetall === null || !is_numeric($vc_hartmetall) || $vc_hartmetall <= 0) {
  http_response_code(400);
  echo 'Ungültiges vc Hartmetall';
  exit;
}
$vc_hartmetall = floatval($vc_hartmetall);

$kc = $_POST['kc'] ?? null;
if ($kc === null || !is_numeric($kc) || $kc <= 0) {
  http_response_code(400);
  echo 'Ungültiger kc';
  exit;
}
$kc = floatval($kc);

$id = $_POST['id'] ?? null;
if ($id !== null && $id !== '' && !ctype_digit((string)$id)) {
  http_response_code(400);
  echo 'Ungültige ID';
  exit;
}

// INSERT oder UPDATE
if ($id) {
  // UPDATE
  $stmt = $pdo->prepare("UPDATE materialien SET name=?, typ=?, gruppe=?, vc_hss=?, vc_hartmetall=?, kc=? WHERE id=?");
  $stmt->execute([
    $name,
    $typ,
    $gruppe,
    $vc_hss,
    $vc_hartmetall,
    $kc,
    (int)$id
  ]);
} else {
  // INSERT
  $stmt = $pdo->prepare("INSERT INTO materialien (name, typ, gruppe, vc_hss, vc_hartmetall, kc) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->execute([
    $name,
    $typ,
    $gruppe,
    $vc_hss,
    $vc_hartmetall,
    $kc
  ]);
}
echo "OK";
?>
