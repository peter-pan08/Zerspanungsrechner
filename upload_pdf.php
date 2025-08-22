<?php
define('REQUIRE_SESSION', true);
$pageTitle = 'PDF-Upload';
include 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }
    if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        if ($_FILES['pdf']['size'] > $maxFileSize) {
            echo "<p style='color:red'>❌ Datei ist größer als 2MB.</p>";
        } else {
            $file = $_FILES['pdf']['tmp_name'];
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $file);
            finfo_close($finfo);
            if ($mime !== 'application/pdf') {
                echo "<p style='color:red'>❌ Nur PDFs sind erlaubt.</p>";
            } else {
                $uploadDir = __DIR__ . '/../uploads';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0700, true);
                }
                $uuid = bin2hex(random_bytes(16));
                $dest = $uploadDir . '/' . $uuid . '.pdf';

                if (move_uploaded_file($file, $dest)) {
                    chmod($dest, 0600);
                    echo "<p style='color:lightgreen'>✅ Datei erfolgreich hochgeladen: <strong>" . htmlspecialchars($_FILES['pdf']['name']) . "</strong></p>";

                    if (file_exists('vendor/autoload.php')) {
                        require_once 'vendor/autoload.php';
                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf = $parser->parseFile($dest);
                        $text = $pdf->getText();
                        echo "<h3>📄 Ausgelesener Text:</h3><pre style='white-space:pre-wrap;background:#1b263b;padding:10px;border-radius:8px;color:#e0e1dd;'>" . htmlspecialchars($text) . "</pre>";
                    } else {
                        echo "<p style='color:orange'>⚠️ PDF-Parser nicht installiert. Bitte zuerst <code>composer require smalot/pdfparser</code> ausführen.</p>";
                    }
                    unlink($dest);
                } else {
                    echo "<p style='color:red'>❌ Fehler beim Hochladen der Datei.</p>";
                }
            }
        }
    }
}
?>
<style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; max-width: 800px; margin: auto; padding-top: 40px; }
    input, button { width: 100%; padding: 10px; margin: 10px 0; background: #415a77; color: white; border: 1px solid #778da9; }
    button { background: #00b4d8; color: black; font-weight: bold; }
    .top-nav a { margin-right: 10px; color: #00b4d8; text-decoration: none; font-weight: bold; }
</style>

<h2>📤 PDF-Upload & Textanalyse</h2>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= generate_csrf_token(); ?>">
    <label>Wähle PDF-Datei aus:</label>
    <input type="file" name="pdf" accept="application/pdf" required>
    <button type="submit">⬆️ Hochladen & Auslesen</button>
</form>
</body>
</html>

