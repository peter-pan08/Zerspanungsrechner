<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'Benutzerverwaltung';
  $pageHeadExtra = <<<'HTML'
<style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 800px; margin: auto; padding-top: 40px; }
    input, select { padding: 6px; width: 100%; margin-bottom: 10px; background: #415a77; color: white; border: 1px solid #778da9; }
    button { padding: 8px; background: #00b4d8; color: black; font-weight: bold; border: none; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { padding: 10px; border: 1px solid #778da9; vertical-align: top; }
    .top-nav a { margin-right: 10px; color: #00b4d8; text-decoration: none; font-weight: bold; }
    h2 { margin-bottom: 10px; }
</style>
HTML;
  include 'header.php';
  require 'session_check.php';
  require 'config.php';
  if ($_SESSION['rolle'] !== 'admin') {
      header('Location: index.php');
      exit;
  }
?>

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
        <td><?= htmlspecialchars($n['id']) ?></td>
        <td><?= htmlspecialchars($n['username']) ?></td>
        <td><?= htmlspecialchars($n['rolle']) ?></td>
        <td>
          <form method="post" style="margin-bottom:5px">
            <input type="hidden" name="id" value="<?= htmlspecialchars($n['id']) ?>">
            <input type="password" name="password" placeholder="Neues Passwort (leer = bleibt)">
            <select name="rolle">
              <option value="admin"<?= $n['rolle'] === 'admin' ? ' selected' : '' ?>>Admin</option>
              <option value="editor"<?= $n['rolle'] === 'editor' ? ' selected' : '' ?>>Editor</option>
              <option value="viewer"<?= $n['rolle'] === 'viewer' ? ' selected' : '' ?>>Viewer</option>
            </select>
            <button type="submit" name="edit_benutzer">💾 Speichern</button>
          </form>
          <form method="post">
            <input type="hidden" name="id" value="<?= htmlspecialchars($n['id']) ?>">
            <button type="submit" name="loeschen" onclick="return confirm('Soll dieser Benutzer wirklich gelöscht werden?')">🗑️ Löschen</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</body>
</html>
