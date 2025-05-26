<?php
require 'config.php';
session_start();

// Bereits eingeloggt? Dann weiter zur Zerspanung (HTML-Datei)
if (isset($_SESSION['user_id'])) {
    header('Location: zerspanung.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Bitte Benutzername und Passwort eingeben.';
    } else {
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_errno) {
            $error = 'Fehler bei der Datenbankverbindung.';
        } else {
            $stmt = $mysqli->prepare(
                "SELECT id, password_hash, rolle 
                   FROM users 
                  WHERE username = ? 
                  LIMIT 1"
            );
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($id, $hash, $rolle);

            if ($stmt->fetch() && password_verify($password, $hash)) {
                $_SESSION['user_id']   = $id;
                $_SESSION['username']  = $username;
                $_SESSION['rolle']     = $rolle;
                header('Location: zerspanung.html');
                exit;
            } else {
                $error = 'Ungültiger Benutzername oder Passwort.';
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Login | Zerspanungsrechner</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: sans-serif;
      margin: 0;
      background: #0a0f14;
      color: #e0e1dd;
    }
    /* Header-Navigation */
    .top-nav {
      background: #1b263b;
      padding: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .top-nav img.logo {
      height: 40px;
      width: auto;
    }
    .top-nav a {
      color: #00b4d8;
      text-decoration: none;
      font-weight: bold;
    }
    .top-nav a:hover {
      text-decoration: underline;
    }
    /* Login-Box */
    .login-box {
      max-width: 400px;
      margin: 40px auto;
      background: #1b263b;
      padding: 20px;
      border-radius: 8px;
    }
    .login-box h2 {
      margin-top: 0;
      color: #e0e1dd;
    }
    .login-box label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    .login-box input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      background: #415a77;
      border: 1px solid #778da9;
      color: #e0e1dd;
      border-radius: 4px;
    }
    .login-box button {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background: #00b4d8;
      border: none;
      font-weight: bold;
      color: #000;
      border-radius: 4px;
      cursor: pointer;
    }
    .login-box button:hover {
      background: #0096c7;
    }
    .error {
      color: #ff4d6d;
      font-weight: bold;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="top-nav">
    <img src="dryba_logo_100.svg" alt="Dryba Logo" class="logo">
    <a href="index.html">🏠 Startseite</a>
    <a href="register.php">📝 Registrieren</a>
  </div>

  <div class="login-box">
    <h2>Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif ?>
    <form method="post" action="login.php">
      <label for="username">Benutzername</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($username) ?>" required>

      <label for="password">Passwort</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Einloggen</button>
    </form>
  </div>
</body>
</html>
