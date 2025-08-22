<?php
require_once 'require_config.php';
if (LOGIN_REQUIRED) {
    define('REQUIRE_SESSION', true);
}
$pageTitle = 'Einstellungen';
include 'header.php';
if (LOGIN_REQUIRED) {
    require 'session_check.php';
    if ($_SESSION['rolle'] !== 'admin') {
        die('Zugriff verweigert');
    }
}

$meldung = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }
    $aktiv = isset($_POST['login_required']);
    $config = file_get_contents('config.php');
    if ($config !== false) {
        $newVal = $aktiv ? 'true' : 'false';
        $config = preg_replace("/define\('LOGIN_REQUIRED',\s*(true|false)\);/", "define('LOGIN_REQUIRED', $newVal);", $config);
        if (file_put_contents('config.php', $config) !== false) {
            $meldung = 'Einstellungen gespeichert.';
            define('LOGIN_REQUIRED', $aktiv);
        } else {
            $meldung = 'Fehler beim Schreiben der config.php';
        }
    } else {
        $meldung = 'config.php nicht gefunden';
    }
}
?>
<style>
  body { background:#0a0f14; color:#e0e1dd; font-family:sans-serif; max-width:600px; margin:auto; padding-top:40px; }
  form { margin-top:20px; }
  label { display:block; margin-bottom:10px; font-weight:bold; }
  button { padding:10px; background:#00b4d8; color:#000; border:none; font-weight:bold; border-radius:4px; }
  .info { margin-top:15px; font-weight:bold; }
</style>
<h2>⚙️ Einstellungen</h2>
<?php if ($meldung): ?>
<p class="info"><?= htmlspecialchars($meldung) ?></p>
<?php endif; ?>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
  <label><input type="checkbox" name="login_required" <?= LOGIN_REQUIRED ? 'checked' : '' ?>> Login/Benutzerverwaltung aktiv</label>
  <button type="submit">Speichern</button>
</form>
</body>
</html>
