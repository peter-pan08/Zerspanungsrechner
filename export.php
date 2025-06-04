<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'Datenexport';
  include 'header.php';
?>
<style>
    body { background: #0a0f14; color: #e0e1dd; font-family: sans-serif; text-align: center; padding-top: 50px; }
    h2 { margin-bottom: 30px; }
    a.button {
      display: inline-block;
      margin: 10px;
      padding: 15px 25px;
      background: #00b4d8;
      color: black;
      font-weight: bold;
      text-decoration: none;
      border-radius: 6px;
    }
    a.button:hover {
      background: #90e0ef;
    }
    .top-nav { margin-bottom: 30px; }
    .top-nav a {
      margin: 0 10px;
      color: #00b4d8;
      text-decoration: none;
      font-weight: bold;
    }
  </style>

  <h2>📤 Ergebnisdaten exportieren</h2>
  <p>Wähle ein Format:</p>

  <a href="export_pdf.php" class="button">📄 PDF</a>
  <a href="export_excel.php" class="button">📊 Excel (XLSX)</a>
  <a href="export_csv.php" class="button">📁 CSV</a>
</body>
</html>
