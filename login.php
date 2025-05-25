<?php
require 'config.php';
session_start();

// Wenn bereits eingeloggt, weiterleiten
if (!empty($_SESSION['user_id'])) {
    header('Location: zerspanung.html');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    // DB-Verbindung
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_error) {
        $error = 'Datenbankverbindung fehlgeschlagen';
    } else {
        $stmt = $mysqli->prepare("SELECT id, password_hash, rolle FROM users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->bind_result($id, $hash, $rolle);
        if ($stmt->fetch() && password_verify($password, $hash)) {
            // Login erfolgreich
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            $_SESSION['rolle'] = $rolle;
            header('Location: zerspanung.html');
            exit;
        } else {
            $error = 'Ungültiger Benutzername oder Passwort';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body { font-family: sans-serif; background: #0a0f14; color: #e0e1dd; padding: 20px; }
    .login-box { max-width: 400px; margin: auto; background: #1b263b; padding: 20px; border-radius: 8px; }
    label { display: block; margin-top: 10px; }
    input { width: 100%; padding: 8px; margin-top: 5px; background: #415a77; border: 1px solid #778da9; color: #e0e1dd; }
    button { margin-top: 15px; width: 100%; padding: 10px; background: #00b4d8; border: none; font-weight: bold; color: #000; cursor: pointer; }
    .error { color: #ff4d6d; margin-top: 10px; }
    .top-nav { margin-bottom: 20px; }
    .top-nav a { color: #00b4d8; text-decoration: none; margin-right: 10px; }
  </style>
</head>
<body>
  <div class="top-nav">
    <a href="index.html">🏠 Startseite</a>
    <a href="register.php">📝 Registrieren</a>
  </div>
  <div class="login-box">
    <h2>Login</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif ?>
    <form method="post">
      <label for="username">Benutzername:</label>
      <input type="text" id="username" name="username" required>
      <label for="password">Passwort:</label>
      <input type="password" id="password" name="password" required>
      <button type="submit">Einloggen</button>
    </form>
  </div>
</body>
</html>
