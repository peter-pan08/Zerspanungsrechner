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
  <style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 400px; margin: auto; padding-top: 60px; }
    input { width: 100%; padding: 10px; margin: 10px 0; background: #415a77; border: 1px solid #778da9; color: white; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; }
    .error { color: #ff4d6d; font-weight: bold; }
  </style>
</head>
<body>
  <h2>🔐 Login Adminbereich</h2>
<?php if ($error) echo "<div class='error'>$error</div>"; ?>
  <form method="post">
    <input type="text" name="username" placeholder="Benutzername" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <button type="submit">Anmelden</button>
  </form>
</body>
</html>
