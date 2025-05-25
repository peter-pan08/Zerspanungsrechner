<?php
  // session_check.php nur dort, wo nötig
  if (defined('REQUIRE_SESSION')) {
    require 'session_check.php';
  }
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title><?= $pageTitle ?? 'Zerspanungsrechner' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    /* Logo */
    .logo {
      height: 50px;
      width: auto;
      margin-right: 1rem;
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
    .top-nav a:hover { text-decoration: underline; }
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
