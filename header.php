<?php
  require_once 'require_config.php';
  require_once 'csrf.php';
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
  <?php $LOGIN_REQUIRED_SAFE = defined('LOGIN_REQUIRED') ? LOGIN_REQUIRED : false; ?>
  <div class="top-nav">
    <img src="dryba_logo_100.svg" alt="Dryba Logo" class="logo">
    <a href="index.php">🏠 Startseite</a>
    <a href="zerspanung.php">🤖 Drehbank</a>
    <a href="fraesen.php">🛠️ Fräsen</a>
    <a href="admin.php">⚙️ Admin</a>
    <a href="settings.php">🔧 Einstellungen</a>
    <?php if ($LOGIN_REQUIRED_SAFE): ?>
      <a href="admin_user.php">👥 Benutzer</a>
      <a href="profil.php">👤 Profil</a>
      <a href="register.php">📝 Registrieren</a>
      <a href="login.php">🔐 Login</a>
      <a href="logout.php">🚪 Logout</a>
    <?php endif; ?>
  </div>
  <div class="page-branding" aria-label="Branding Dryba">
    <span>&copy; <?= date('Y') ?> M. Dryba</span>
    <img src="Logo/dryba_logo.svg" alt="Dryba Logo">
  </div>
