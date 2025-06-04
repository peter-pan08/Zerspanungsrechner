<?php
  // define('REQUIRE_SESSION', true);
  $pageTitle = 'Registrieren';
  include 'header.php';
require 'config.php';
$meldung = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $username = $_POST['username'];
  $passwort = $_POST['password'];

  // Prüfen, ob Benutzer existiert
  $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
  $check->execute([$username]);
  if ($check->fetch()) {
    $meldung = "⚠️ Benutzername bereits vergeben.";
  } else {
    $hash = password_hash($passwort, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, rolle) VALUES (?, ?, 'viewer')");
    $stmt->execute([$username, $hash]);
    $meldung = "✅ Registrierung erfolgreich. Du kannst dich jetzt einloggen.";
  }
}
?>
<style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 400px; margin: auto; padding-top: 60px; }
    input { width: 100%; padding: 10px; margin: 10px 0; background: #415a77; border: 1px solid #778da9; color: white; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; }
    .info { margin-top: 20px; font-weight: bold; }
  </style>
  <h2>📝 Registrieren</h2>
  <form method="post">
    <input type="text" name="username" placeholder="Benutzername" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <button type="submit">Konto erstellen</button>
  </form>
  <div class="info"><?= $meldung ?></div>
</body>
</html>
