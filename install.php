<?php
if (file_exists('config.php')) {
  require 'config.php';
  if (defined('DEBUG') && DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
  }
}

require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Installationsassistent</title>
  <style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 600px; margin: auto; padding-top: 40px; }
    input { width: 100%; padding: 10px; margin: 8px 0; background: #415a77; border: 1px solid #778da9; color: white; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; margin-top: 10px; }
  </style>
</head>
<body>
  <h2>🛠️ Web-Installer: Zerspanungsrechner</h2>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <label>Datenbank-Host:</label>
    <input name="dbhost" value="localhost" required>
    <label>Datenbank-Benutzer (z. B. root):</label>
    <input name="dbuser" required>
    <label>Datenbank-Passwort:</label>
    <input type="password" name="dbpass">
    <label>Datenbank-Name (wird erstellt wenn nötig):</label>
    <input name="dbname" required>
    <label>App-Benutzername für Zugriff:</label>
    <input name="appuser" required>
    <label>App-Benutzerpasswort:</label>
    <input type="password" name="apppass" required>
    <label>Betriebsmodus:</label>
    <select name="demo_mode">
      <option value="false">Standard</option>
      <option value="true">Demo-Modus</option>
    </select>
    <label>Benutzerverwaltung aktivieren?</label>
    <select name="login_required">
      <option value="true">Ja</option>
      <option value="false">Nein</option>
    </select>
    <label>Beispieldaten importieren?</label>
    <select name="import_demo">
      <option value="false">Nein</option>
      <option value="true">Ja</option>
    </select>
  <button type="submit">Installation starten</button>
  </form>
</body>
</html>
<?php exit; }

if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
  die('Ungültiger CSRF-Token');
}

$dbhost = $_POST['dbhost'];
$dbuser = $_POST['dbuser'];
$dbpass = $_POST['dbpass'];
$dbname = $_POST['dbname'];
$appuser = $_POST['appuser'];
$apppass = $_POST['apppass'];
$login_required = isset($_POST['login_required']) && $_POST['login_required'] === 'false' ? 'false' : 'true';
$demo_mode = isset($_POST['demo_mode']) && $_POST['demo_mode'] === 'true' ? 'true' : 'false';
$import_demo = isset($_POST['import_demo']) && $_POST['import_demo'] === 'true';

$existing_demo = null;
$existing_login = null;
if (file_exists('config.php')) {
  include 'config.php';
  if (defined('DEMO_MODE')) {
    $existing_demo = DEMO_MODE ? 'true' : 'false';
  }
  if (defined('LOGIN_REQUIRED')) {
    $existing_login = LOGIN_REQUIRED ? 'true' : 'false';
  }
}

$dsn = "mysql:host=$dbhost";
try {
  $conn = new PDO($dsn, $dbuser, $dbpass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
  $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
  $conn->exec("USE `$dbname`");
} catch (PDOException $e) {
  die("<p>❌ Verbindungsfehler: " . $e->getMessage() . "</p>");
}

$tables = [
  "CREATE TABLE IF NOT EXISTS materialien (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    gruppe VARCHAR(1),
    vc_hss FLOAT,
    vc_hartmetall FLOAT,
    kc FLOAT
  )",
  "CREATE TABLE IF NOT EXISTS platten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    typ VARCHAR(50),
    gruppen VARCHAR(20),
    vc FLOAT
  )",
  "CREATE TABLE IF NOT EXISTS fraeser (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    typ VARCHAR(50),
    durchmesser FLOAT,
    zaehne INT,
    gruppen VARCHAR(20),
    vc FLOAT,
    fz FLOAT
  )",
];

if ($login_required === 'true') {
  $tables[] = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    rolle VARCHAR(20) DEFAULT 'admin'
  )";
}

foreach ($tables as $sql) {
  try {
    $conn->exec($sql);
  } catch (PDOException $e) {
    echo "<p>❌ Fehler bei SQL: " . $e->getMessage() . "</p>";
  }
}

try {
  $conn->exec("CREATE USER IF NOT EXISTS '$appuser'@'localhost' IDENTIFIED BY '$apppass'");
  $conn->exec("GRANT SELECT, INSERT, UPDATE, DELETE ON `$dbname`.* TO '$appuser'@'localhost'");
  $conn->exec("FLUSH PRIVILEGES");
} catch (PDOException $e) {
  echo "<p>❌ Fehler beim Anlegen des DB-Benutzers: " . $e->getMessage() . "</p>";
}

if ($login_required === 'true') {
  $ph = password_hash("admin123", PASSWORD_DEFAULT);
  $stmt = $conn->prepare("INSERT IGNORE INTO users (username, password_hash, rolle) VALUES ('admin', ?, 'admin')");
  $stmt->execute([$ph]);
}

if ($import_demo && file_exists('beispieldaten.sql')) {
  $demoSql = file_get_contents('beispieldaten.sql');
  try {
    $conn->exec($demoSql);
    echo "<p>Beispieldaten wurden importiert.</p>";
  } catch (PDOException $e) {
    echo "<p>Fehler beim Import der Beispieldaten: " . $e->getMessage() . "</p>";
  }
}

$demo_setting = $existing_demo !== null ? $existing_demo : $demo_mode;
$login_setting = $existing_login !== null ? $existing_login : $login_required;
$config = <<<PHP
<?php
define('DEBUG', false);
define('DEMO_MODE', $demo_setting);  // Demo-Modus aktiv: kein Löschen möglich
define('LOGIN_REQUIRED', $login_setting);

\$host = '$dbhost';
\$user = '$appuser';
\$pass = '$apppass';
\$db = '$dbname';
?>
PHP;

$config_written = false;
if (!file_exists("config.php")) {
  file_put_contents("config.php", $config);
  $config_written = true;
}

$messageCfg = $config_written
  ? "<p><strong>config.php</strong> wurde erfolgreich gespeichert.</p>"
  : "<p><strong>config.php</strong> ist bereits vorhanden und wurde nicht überschrieben.</p>";

$loginInfo = '';
if ($login_setting === 'true') {
  $loginInfo = "<p><em>⚠️ Der Benutzer <strong>admin</strong> mit Passwort <strong>admin123</strong> ist ein Demo-Konto.<br>Bitte erstelle einen echten Admin und lösche das Demo-Konto anschließend aus der Benutzerverwaltung.</em></p>\n<p>Du kannst dich jetzt mit dem Admin-Benutzer <strong>admin</strong></p>\n<p>und dem Passwort <strong>admin123</strong></p>\n<p>unter <code>login.php</code> anmelden.</p>";
}

echo <<<HTML
<h2>✅ Installation abgeschlossen</h2>
$messageCfg
<p>Verwendeter Datenbankbenutzer (App): <strong>$appuser</strong></p>
<p>Die Datenbank <strong>$dbname</strong> wurde vorbereitet.</p>
$loginInfo
<p><em>Tipp: Schütze die Datei <code>config.php</code> mit CHMOD 640</em></p>
HTML;
?>
