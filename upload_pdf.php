<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'Zerspanungsrechner';
  include 'header.php';
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
  $file = $_FILES['pdf']['tmp_name'];
  $dest = __DIR__ . '/uploads/' . basename($_FILES['pdf']['name']);
  if (!file_exists(__DIR__ . '/uploads')) {
    mkdir(__DIR__ . '/uploads', 0777, true);
  }

  if (move_uploaded_file($file, $dest)) {
    echo "<p style='color:lightgreen'>✅ Datei erfolgreich hochgeladen: <strong>" . basename($dest) . "</strong></p>";

    // Wenn pdfparser vorhanden, extrahiere Text
    if (file_exists("vendor/autoload.php")) {
      require_once 'vendor/autoload.php';
      $parser = new \Smalot\PdfParser\Parser();
      $pdf = $parser->parseFile($dest);
      $text = $pdf->getText();
      echo "<h3>📄 Ausgelesener Text:</h3><pre style='white-space:pre-wrap;background:#1b263b;padding:10px;border-radius:8px;color:#e0e1dd;'>" . htmlspecialchars($text) . "</pre>";
    } else {
      echo "<p style='color:orange'>⚠️ PDF-Parser nicht installiert. Bitte zuerst <code>composer require smalot/pdfparser</code> ausführen.</p>";
    }
  } else {
    echo "<p style='color:red'>❌ Fehler beim Hochladen der Datei.</p>";
  }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>PDF-Upload</title>
  <style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 800px; margin: auto; padding-top: 40px; }
    input, button { width: 100%; padding: 10px; margin: 10px 0; background: #415a77; color: white; border: 1px solid #778da9; }
    button { background: #00b4d8; color: black; font-weight: bold; }
    .top-nav a { margin-right: 10px; color: #00b4d8; text-decoration: none; font-weight: bold; }
  </style>
</head>
<body>

  <h2>📤 PDF-Upload & Textanalyse</h2>
  <form method="post" enctype="multipart/form-data">
    <label>Wähle PDF-Datei aus:</label>
    <input type="file" name="pdf" accept="application/pdf" required>
    <button type="submit">⬆️ Hochladen & Auslesen</button>
  </form>
</body>
</html>
