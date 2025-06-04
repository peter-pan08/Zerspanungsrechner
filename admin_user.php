<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'Zerspanungsrechner';
  include 'header.php';

  require 'config.php';

  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $meldung = "";

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Neuer Benutzer anlegen (auch im Demo-Modus erlaubt)
    if (isset($_POST['neuer_benutzer'])) {
      $username = trim($_POST['username']);
      $passwort = trim($_POST['password']);
      $rolle    = $_POST['rolle'] ?? 'viewer';

      $check = $pdo->prepare("SELECT id FROM users WHERE username = ?");
      $check->execute([$username]);
      if ($check->fetch()) {
        $meldung = "⚠️ Benutzername bereits vergeben.";
      } else {
        $hash = password_hash($passwort, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, rolle) VALUES (?, ?, ?)");
        $stmt->execute([$username, $hash, $rolle]);
        $meldung = "✅ Benutzer hinzugefügt.";
      }
    }

    // Bestehenden Benutzer bearbeiten
    if (isset($_POST['edit_benutzer'])) {
      if (defined('DEMO_MODE') && DEMO_MODE) {
        $meldung = "🚫 Bearbeiten im Demo-Modus nicht erlaubt.";
      } else {
        $id    = (int)$_POST['id'];
        $rolle = $_POST['rolle'];
        $pass  = trim($_POST['password']);

        if ($pass !== '') {
          $hash = password_hash($pass, PASSWORD_DEFAULT);
          $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, rolle = ? WHERE id = ?");
          $stmt->execute([$hash, $rolle, $id]);
        } else {
          $stmt = $pdo->prepare("UPDATE users SET rolle = ? WHERE id = ?");
          $stmt->execute([$rolle, $id]);
        }
        $meldung = "✅ Benutzer aktualisiert.";
      }
    }

    // Benutzer löschen
    if (isset($_POST['loeschen'])) {
      if (defined('DEMO_MODE') && DEMO_MODE) {
        $meldung = "🚫 Löschen im Demo-Modus nicht erlaubt.";
      } else {
        $id = (int)$_POST['id'];
        $stmt = $pdo->prepare("SELECT rolle FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $rolle = $stmt->fetchColumn();

        if ($rolle === 'admin') {
          $count = $pdo->query("SELECT COUNT(*) FROM users WHERE rolle = 'admin'")->fetchColumn();
          if ($count <= 1) {
            $meldung = "⚠️ Letzter Admin kann nicht gelöscht werden.";
          } else {
            $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $del->execute([$id]);
            $meldung = "✅ Benutzer gelöscht.";
          }
        } else {
          $del = $pdo->prepare("DELETE FROM users WHERE id = ?");
          $del->execute([$id]);
          $meldung = "✅ Benutzer gelöscht.";
        }
      }
    }
  }

  $nutzer = $pdo->query("SELECT id, username, rolle FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
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
    .info { margin-top: 10px; font-weight: bold; }
  </style>
</head>
<body>

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
  <div class="info"><?= $meldung ?></div>

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
            <button type="submit" name="loeschen" onclick="return confirm('Soll dieser Benutzer wirklich gelöscht werden?')">🗑️ Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
