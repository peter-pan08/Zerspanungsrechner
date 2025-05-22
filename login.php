<?php
session_start();
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  require 'config.php';
  try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
      $_SESSION['user'] = $user['username'];
      $_SESSION['rolle'] = $user['rolle'];
      header('Location: admin.html');
      exit;
    } else {
      $error = "❌ Benutzername oder Passwort ist falsch.";
    }
  } catch (PDOException $e) {
    $error = "❌ Datenbankfehler: " . $e->getMessage();
  }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
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
