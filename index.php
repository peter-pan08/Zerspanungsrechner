<?php
  require_once 'require_config.php';
  // Für Seiten, die Session-Handling benötigen:
  if (defined('REQUIRE_SESSION')) {
    require 'session_check.php';
  }
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Zerspanungsrechner – Demo</title>
  <style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; text-align: center; padding-top: 50px; }
    h1 { font-size: 2em; margin-bottom: 20px; }
    a.button {
      display: inline-block;
      margin: 10px;
      padding: 15px 25px;
      background: #00b4d8;
      color: black;
      font-weight: bold;
      text-decoration: none;
      border-radius: 6px;
    }
    a.button:hover {
      background: #90e0ef;
    }
    p.hinweis {
      color: orange;
      margin-top: 20px;
    }
  </style>
</head>
<body>
  <h1>🔧 Zerspanungsrechner – Demo-Version</h1>
  <p>Willkommen zur öffentlichen Demoversion. Löschen und Bearbeiten sind deaktiviert.</p>
  <a href="zerspanung.php" class="button">🤖 Drehbank</a>
  <a href="fraesen.php" class="button">🛠️ Fräsen</a>
  <?php if (LOGIN_REQUIRED): ?>
  <a href="login.php" class="button">🔐 Login</a>
  <?php endif; ?>
  <p class="hinweis">⚠️ Im Demo-Modus können keine Daten gelöscht oder geändert werden.</p>
</body>
</html>
