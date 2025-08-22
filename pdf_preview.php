<?php
require_once 'vendor/autoload.php';
require_once 'csrf.php';

function extract_data($text) {
  $data = [
    'werkstoff' => '',
    'vc' => '',
    'vorschub' => '',
    'leistung' => '',
    'drehmoment' => '',
    'platte' => ''
  ];

  if (preg_match('/Werkstoff\s*:?\s*(.*?)\n/', $text, $m)) $data['werkstoff'] = trim($m[1]);
  if (preg_match('/Schnittgeschwindigkeit.*?(\d+[\.,]?\d*)/', $text, $m)) $data['vc'] = str_replace(',', '.', $m[1]);
  if (preg_match('/Vorschub.*?(\d+[\.,]?\d*)\s*mm\/U/', $text, $m)) $data['vorschub'] = str_replace(',', '.', $m[1]);
  if (preg_match('/Leistungsaufnahme.*?(\d+[\.,]?\d*)\s*kW/', $text, $m)) $data['leistung'] = str_replace(',', '.', $m[1]);
  if (preg_match('/Drehmoment.*?(\d+[\.,]?\d*)\s*Nm/', $text, $m)) $data['drehmoment'] = str_replace(',', '.', $m[1]);
  if (preg_match('/Platte\s*:?\s*(.*?)\n/', $text, $m)) $data['platte'] = trim($m[1]);

  return $data;
}

$extracted = null;
$save_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
    die('Ungültiger CSRF-Token');
  }
  if (isset($_POST['save'])) {
    if (!empty($_POST['werkstoff'])) {
      require_once 'db.php';
      $pdo = getPDO();

      $stmt1 = $pdo->prepare("INSERT INTO materialien (name, gruppe, vc_hartmetall, kc) VALUES (?, ?, ?, ?)");
      $stmt1->execute([
        $_POST['werkstoff'],
        strtoupper(substr($_POST['werkstoff'], 0, 1)),
        $_POST['vc'],
        round($_POST['leistung'] * 60000 / ($_POST['vc'] ?: 1))
      ]);

      $stmt2 = $pdo->prepare("INSERT INTO platten (name, typ, gruppen, vc) VALUES (?, ?, ?, ?)");
      $stmt2->execute([
        $_POST['platte'],
        $_POST['platte'],
        strtoupper(substr($_POST['werkstoff'], 0, 1)),
        $_POST['vc']
      ]);

      $save_success = true;
    }

    $extracted = [
      'werkstoff'  => $_POST['werkstoff'] ?? '',
      'vc'         => $_POST['vc'] ?? '',
      'vorschub'   => $_POST['vorschub'] ?? '',
      'leistung'   => $_POST['leistung'] ?? '',
      'drehmoment' => $_POST['drehmoment'] ?? '',
      'platte'     => $_POST['platte'] ?? ''
    ];
  } elseif (isset($_FILES['pdf'])) {
    $file = $_FILES['pdf']['tmp_name'];
    $dest = __DIR__ . '/uploads/' . basename($_FILES['pdf']['name']);
    if (!file_exists(__DIR__ . '/uploads')) {
      mkdir(__DIR__ . '/uploads', 0777, true);
    }

    if (move_uploaded_file($file, $dest)) {
      $parser = new \Smalot\PdfParser\Parser();
      $pdf = $parser->parseFile($dest);
      $text = $pdf->getText();
      $extracted = extract_data($text);
    }
  }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>PDF-Wertvorschau</title>
  <style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 800px; margin: auto; padding-top: 40px; }
    input { width: 100%; padding: 8px; margin: 6px 0; background: #415a77; border: 1px solid #778da9; color: white; }
    button { width: 100%; padding: 10px; background: #00b4d8; color: black; font-weight: bold; border: none; margin-top: 10px; }
    .top-nav a { margin-right: 10px; color: #00b4d8; text-decoration: none; font-weight: bold; }
  </style>
</head>
<body>
  <div class="top-nav">
    <a href="index.php">🏠 Startseite</a>
    <a href="zerspanung.html">🧮 Zerspanung</a>
    <a href="admin.html">⚙️ Admin</a>
    <a href="admin_user.php">👥 Benutzer</a>
    <a href="profil.php">👤 Profil</a>
    <a href="register.php">📝 Registrieren</a>
    <a href="login.php">🔐 Login</a>
    <a href="logout.php">🚪 Logout</a>
  </div>

  <h2>🔍 PDF-Wertvorschau</h2>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <label>PDF-Datei hochladen:</label>
    <input type="file" name="pdf" accept="application/pdf" required>
    <button type="submit">📤 Hochladen & Auslesen</button>
  </form>

  <?php if ($save_success): ?>
    <p style='color:lightgreen;font-weight:bold;'>✅ Werte wurden in die Datenbank gespeichert.</p>
    <p><a href='pdf_preview.php' style='color:#00b4d8;font-weight:bold;'>🔁 Neues PDF hochladen</a> | <a href='admin.html' style='color:#00b4d8;font-weight:bold;'>🏁 Zurück zur Übersicht</a></p>
  <?php endif; ?>

  <?php if ($extracted): ?>
  <h3>📋 Vorschau erkannter Werte:</h3>
  <form method="post">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <label>Werkstoff:</label>
    <input name="werkstoff" value="<?= htmlspecialchars($extracted['werkstoff']) ?>">
    <label>Schnittgeschwindigkeit (vc):</label>
    <input name="vc" value="<?= htmlspecialchars($extracted['vc']) ?>">
    <label>Vorschub (mm/U):</label>
    <input name="vorschub" value="<?= htmlspecialchars($extracted['vorschub']) ?>">
    <label>Leistungsaufnahme (kW):</label>
    <input name="leistung" value="<?= htmlspecialchars($extracted['leistung']) ?>">
    <label>Drehmoment (Nm):</label>
    <input name="drehmoment" value="<?= htmlspecialchars($extracted['drehmoment']) ?>">
    <label>Plattentyp:</label>
    <input name="platte" value="<?= htmlspecialchars($extracted['platte']) ?>">
    <button type="submit" name="save">💾 Speichern</button>
  </form>
  <?php endif; ?>
</body>
</html>
