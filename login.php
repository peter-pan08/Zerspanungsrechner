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
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <?php include 'header.php'; ?>
  <main>
    <section class="login-box">
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
    </section>
  </main>
  <?php include 'footer.php'; ?>
</body>
</html>
