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
  <title><?= htmlspecialchars($pageTitle ?? 'Zerspanungsrechner') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Logo im Header */
    .logo {
      height: 50px;       /* Zielfläche 50px hoch */
      width: auto;        /* Seitenverhältnis beibehalten */
      margin-right: 1rem; /* Abstand zu den Links */
      vertical-align: middle;
    }
    /* Gemeinsamer Header */
    .top-nav {
      background: #1b263b;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 8px;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 10px;
    }
    .top-nav a {
      color: #00b4d8;
      text-decoration: none;
      font-weight: bold;
    }
    .top-nav a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="top-nav">
    <img src="dryba_logo_100.svg" alt="Dryba Logo" class="logo">
    <a href="index.php">🏠 Startseite</a>
    <a href="zerspanung.php">🤖 Drehbank</a>
    <a href="fraesen.php">🛠️ Fräsen</a>
    <a href="admin.php">⚙️ Admin</a>
    <a href="settings.php">🔧 Einstellungen</a>
    <?php if (LOGIN_REQUIRED): ?>
      <a href="admin_user.php">👥 Benutzer</a>
      <a href="profil.php">👤 Profil</a>
      <a href="register.php">📝 Registrieren</a>
      <a href="login.php">🔐 Login</a>
      <a href="logout.php">🚪 Logout</a>
    <?php endif; ?>
  </div>
