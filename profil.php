<?php
require 'session_check.php';
require 'config.php';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$meldung = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $neuesPasswort = $_POST['password'];
  if (!empty($neuesPasswort)) {
    $hash = password_hash($neuesPasswort, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    $stmt->execute([$hash, $_SESSION['user']]);
    $meldung = "✅ Passwort wurde geändert.";
  } else {
    $meldung = "⚠️ Bitte ein neues Passwort eingeben.";
  }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Mein Profil</title>
  <style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 600px; margin: auto; padding-top: 40px; }
    input { width: 100%; padding: 10px; margin: 8px 0; background: #415a77; border: 1px solid #778da9; color: white; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; margin-top: 10px; }
    .info { margin-top: 15px; font-weight: bold; }
  </style>
</head>
<body>
<div class="top-nav" style="background:#1b263b;padding:10px;margin-bottom:20px;border-radius:8px;display:flex;flex-wrap:wrap;gap:10px;">
  <a href="index.html" style="color:#00b4d8;text-decoration:none;font-weight:bold;">🏠 Startseite</a>
  <a href="zerspanung.html" style="color:#00b4d8;text-decoration:none;font-weight:bold;">🧮 Zerspanung</a>
  <a href="admin.html" style="color:#00b4d8;text-decoration:none;font-weight:bold;">⚙️ Admin</a>
  <a href="admin_user.php" style="color:#00b4d8;text-decoration:none;font-weight:bold;">👥 Benutzer</a>
  <a href="profil.php" style="color:#00b4d8;text-decoration:none;font-weight:bold;">👤 Profil</a>
  <a href="register.php" style="color:#00b4d8;text-decoration:none;font-weight:bold;">📝 Registrieren</a>
  <a href="login.php" style="color:#00b4d8;text-decoration:none;font-weight:bold;">🔐 Login</a>
  <a href="logout.php" style="color:#00b4d8;text-decoration:none;font-weight:bold;">🚪 Logout</a>
</div>
  <h2>👤 Mein Profil</h2>
  <p>Angemeldet als: <strong><?= htmlspecialchars($_SESSION['user']) ?></strong> (<?= $_SESSION['rolle'] ?>)</p>
  <form method="post">
    <label>Neues Passwort:</label>
    <input type="password" name="password" required>
    <button type="submit">🔒 Passwort ändern</button>
  </form>
  <div class="info"><?= $meldung ?></div>
</body>
</html>
