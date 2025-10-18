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
    /* Branding unten rechts wie bei Torprüfungen */
    .page-branding {
      position: fixed;
      right: 1.2rem;
      bottom: 1.2rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.85rem;
      pointer-events: auto;
      z-index: 100;
      transition: transform 0.2s ease;
    }
    .page-branding:hover {
      transform: scale(1.6);
      transform-origin: bottom right;
    }
    .page-branding img {
      height: 48px;
      width: auto;
      display: block;
    }
    .page-branding span {
      text-transform: uppercase;
      font-size: 0.7rem;
      letter-spacing: 0.12em;
      color: rgba(148, 163, 184, 0.8);
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
  <div class="page-branding" aria-label="Branding Dryba">
    <span>&copy; <?= date('Y') ?> M. Dryba</span>
    <img src="Logo/dryba_logo.svg" alt="Dryba Logo">
  </div>
</body>
</html>
