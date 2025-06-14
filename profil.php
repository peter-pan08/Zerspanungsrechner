<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'Mein Profil';
  include 'header.php';
require 'session_check.php';
require 'require_config.php';

// Logged in user's name
$currentUser = $_SESSION['username'];

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$meldung = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $neuesPasswort = $_POST['password'];
  if (!empty($neuesPasswort)) {
    $hash = password_hash($neuesPasswort, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    $stmt->execute([$hash, $currentUser]);
    $meldung = "✅ Passwort wurde geändert.";
  } else {
    $meldung = "⚠️ Bitte ein neues Passwort eingeben.";
  }
}
?>
<style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 600px; margin: auto; padding-top: 40px; }
    input { width: 100%; padding: 10px; margin: 8px 0; background: #415a77; border: 1px solid #778da9; color: white; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; margin-top: 10px; }
    .info { margin-top: 15px; font-weight: bold; }
  </style>
  <h2>👤 Mein Profil</h2>
  <p>Angemeldet als: <strong><?= htmlspecialchars($currentUser) ?></strong> (<?= $_SESSION['rolle'] ?>)</p>
  <form method="post">
    <label>Neues Passwort:</label>
    <input type="password" name="password" required>
    <button type="submit">🔒 Passwort ändern</button>
  </form>
  <div class="info"><?= htmlspecialchars($meldung) ?></div>
</body>
</html>
