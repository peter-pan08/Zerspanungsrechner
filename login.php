<?php
require 'config.php';

// Session starten
session_start();

// Bereits eingeloggt? Dann weiter zur Zerspanung
if (isset($_SESSION['user_id'])) {
    header('Location: zerspanung.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Bitte Benutzername und Passwort eingeben.';
    } else {
        // Mit Datenbank verbinden
        $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($mysqli->connect_errno) {
            $error = 'Fehler bei der Datenbankverbindung: ' . $mysqli->connect_error;
        } else {
            $stmt = $mysqli->prepare("SELECT id, password_hash, rolle FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->bind_result($id, $hash, $rolle);
            if ($stmt->fetch() && password_verify($password, $hash)) {
                // Credentials korrekt
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['rolle'] = $rolle;
                header('Location: zerspanung.php');
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
  <title>Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      font-family: sans-serif;
      margin: 20px;
      background-color: #0a0f14;
      color: #e0e1dd;
      background-image: url('A_digital_vector_illustration_depicts_a_CNC_lathe_.png');
      background-repeat: no-repeat;
      background-size: contain;
      background-position: center center;
      background-attachment: fixed;
      background-blend-mode: multiply;
    }
    .top-nav {
      background: #1b263b;
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 8px;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }
    .top-nav a {
      color: #00b4d8;
      text-decoration: none;
      font-weight: bold;
    }
    .top-nav a:hover { text-decoration: underline; }
    main {
      max-width: 400px;
      margin: auto;
      background: #1b263b;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.5);
    }
    h2 {
      margin-top: 0;
      color: #e0e1dd;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      background: #415a77;
      color: #e0e1dd;
      border: 1px solid #778da9;
      border-radius: 4px;
    }
    button {
      width: 100%;
      padding: 10px;
      margin-top: 20px;
      background: #00b4d8;
      color: #0a0f14;
      border: none;
      font-size: 1em;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background: #0096c7;
    }
    .error {
      color: #ff4d6d;
      font-weight: bold;
      margin-bottom: 10px;
    }
    a.action-link {
      display: block;
      text-align: center;
      margin-top: 15px;
      color: #00b4d8;
    }
  </style>
</head>
<body>
  <div class="top-nav">
    <a href="index.php">🏠 Startseite</a>
    <a href="register.php">📝 Registrieren</a>
  </div>

  <main>
    <h2>Login</h2>
    <?php if ($error): ?>
      <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif ?>
    <form method="post" action="login.php">
      <label for="username">Benutzername</label>
      <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" required>

      <label for="password">Passwort</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Einloggen</button>
    </form>
    <a class="action-link" href="login.php?forgot=1">Passwort vergessen?</a>
  </main>
</body>
</html>
