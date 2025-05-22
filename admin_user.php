<?php
require 'session_check.php';
if ($_SESSION['rolle'] !== 'admin') {
  die('Zugriff verweigert');
}
require 'config.php';

$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Benutzer bearbeiten
if (isset($_POST['edit_benutzer'])) {
  $id = $_POST['id'];
  $rolle = $_POST['rolle'];
  $sql = "UPDATE users SET rolle = ?" . (!empty($_POST['password']) ? ", password_hash = ?" : "") . " WHERE id = ?";
  $params = [ $rolle ];
  if (!empty($_POST['password'])) {
    $params[] = password_hash($_POST['password'], PASSWORD_DEFAULT);
  }
  $params[] = $id;
  $stmt = $pdo->prepare($sql);
  $stmt->execute($params);
}

// Benutzer löschen
if (isset($_POST['loeschen'])) {
  $admin_count = $pdo->query("SELECT COUNT(*) FROM users WHERE rolle = 'admin'")->fetchColumn();
  $stmt = $pdo->prepare("SELECT rolle FROM users WHERE id = ?");
  $stmt->execute([$_POST['id']]);
  $user_to_delete = $stmt->fetch();

  if ($user_to_delete['rolle'] === 'admin' && $admin_count <= 1) {
    echo "<p style='color:red;font-weight:bold;'>❌ Der letzte Admin darf nicht gelöscht werden.</p>";
  } else {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$_POST['id']]);
  }
}

// Benutzer hinzufügen
if (isset($_POST['neuer_benutzer'])) {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $rolle = $_POST['rolle'];
  $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, rolle) VALUES (?, ?, ?)");
  $stmt->execute([$username, $password, $rolle]);
}

$nutzer = $pdo->query("SELECT * FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Benutzerverwaltung</title>
  <style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 800px; margin: auto; padding-top: 40px; }
    input, select { padding: 6px; width: 100%; margin-bottom: 10px; background: #415a77; color: white; border: 1px solid #778da9; }
    button { padding: 8px; background: #00b4d8; color: black; font-weight: bold; border: none; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #778da9; vertical-align: top; }
    .top-nav a { margin-right: 10px; color: #00b4d8; text-decoration: none; font-weight: bold; }
    h2 { margin-bottom: 10px; }
  </style>
</head>
<body>
  <div class="top-nav">
    <a href="index.html">🏠 Startseite</a>
    <a href="zerspanung.html">🧮 Zerspanung</a>
    <a href="admin.html">⚙️ Admin</a>
    <a href="admin_user.php">👥 Benutzer</a>
    <a href="profil.php">👤 Profil</a>
    <a href="register.php">📝 Registrieren</a>
    <a href="login.php">🔐 Login</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <h2>Benutzerverwaltung</h2>

  <form method="post">
    <input type="text" name="username" placeholder="Benutzername" required>
    <input type="password" name="password" placeholder="Passwort" required>
    <select name="rolle">
      <option value="admin">Admin</option>
      <option value="editor">Editor</option>
      <option value="viewer">Viewer</option>
    </select>
    <button type="submit" name="neuer_benutzer">➕ Benutzer hinzufügen</button>
  </form>

  <table>
    <tr><th>ID</th><th>Benutzer</th><th>Rolle</th><th>Aktionen</th></tr>
    <?php foreach ($nutzer as $n): ?>
      <tr>
        <td><?= $n['id'] ?></td>
        <td><?= $n['username'] ?></td>
        <td><?= $n['rolle'] ?></td>
        <td>
          <form method="post" style="margin-bottom:5px">
            <input type="hidden" name="id" value="<?= $n['id'] ?>">
            <input type="password" name="password" placeholder="Neues Passwort (leer = bleibt)">
            <select name="rolle">
              <option value="admin"<?= $n['rolle'] === 'admin' ? ' selected' : '' ?>>Admin</option>
              <option value="editor"<?= $n['rolle'] === 'editor' ? ' selected' : '' ?>>Editor</option>
              <option value="viewer"<?= $n['rolle'] === 'viewer' ? ' selected' : '' ?>>Viewer</option>
            </select>
            <button type="submit" name="edit_benutzer">💾 Speichern</button>
          </form>
          <form method="post">
            <input type="hidden" name="id" value="<?= $n['id'] ?>">
            <button type="submit" name="loeschen">🗑️ Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
