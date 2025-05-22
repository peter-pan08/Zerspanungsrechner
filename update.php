<?php
require 'session_check.php';
if ($_SESSION['rolle'] !== 'admin') {
  die('Zugriff verweigert');
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>System-Update</title>
  <style>
    body { font-family: sans-serif; background: #0a0f14; color: #e0e1dd; max-width: 600px; margin: auto; padding-top: 40px; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; margin-top: 10px; }
  </style>
</head>
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
<body>
  <h2>🔄 Update-System</h2>
  <p>Hier könntest du später Tabellen aktualisieren, neue Felder hinzufügen usw.</p>
  <form method="post">
    <button name="simulate" value="1">🚧 Simulation: Tabellenstruktur prüfen</button>
  </form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo "<p>✅ (Simulation) Tabellenstruktur OK.</p>";
}
?>
</body>
</html>