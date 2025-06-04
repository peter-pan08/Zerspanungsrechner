<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'System-Update';
  include 'header.php';
require 'session_check.php';
if ($_SESSION['rolle'] !== 'admin') {
  die('Zugriff verweigert');
}
?>
<style>
    body { font-family: sans-serif; background: #0a0f14; color: #e0e1dd; max-width: 600px; margin: auto; padding-top: 40px; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; margin-top: 10px; }
  </style>
  <h2>🔄 Update-System</h2>
  <p>Dieses Skript führt Datenbankaktualisierungen für bestehende Installationen aus.</p>
  <form method="post">
    <button name="update" value="1">⚙️ Update ausführen</button>
  </form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
  require 'config.php';
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $res = $pdo->query("SHOW COLUMNS FROM fraeser LIKE 'durchmesser'");
  if ($res->rowCount() === 0) {
    $pdo->exec("ALTER TABLE fraeser ADD COLUMN durchmesser FLOAT");
    echo '<p>Spalte <code>durchmesser</code> wurde hinzugefügt.</p>';
  } else {
    echo '<p>Spalte <code>durchmesser</code> ist bereits vorhanden.</p>';
  }
}
?>
</body>
</html>