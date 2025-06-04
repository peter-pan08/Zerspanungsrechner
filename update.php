<?php
  define('REQUIRE_SESSION', true);
  $pageTitle = 'System-Update';
  $pageHeadExtra = <<<'HTML'
<style>
    body { font-family: sans-serif; background: #0a0f14; color: #e0e1dd; max-width: 600px; margin: auto; padding-top: 40px; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; margin-top: 10px; }
</style>
HTML;
    body { font-family: sans-serif; background: #0a0f14; color: #e0e1dd; max-width: 600px; margin: auto; padding-top: 40px; }
    button { padding: 10px; width: 100%; background: #00b4d8; border: none; color: black; font-weight: bold; margin-top: 10px; }
  </style>
  <h2>🔄 Update-System</h2>
  <p>Hier könntest du später Tabellen aktualisieren, neue Felder hinzufügen usw.</p>
  <form method="post">
    <button name="simulate" value="1">🚧 Simulation: Tabellenstruktur prüfen</button>
  </form>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo "<p>✅ (Simulation) Tabellenstruktur OK.</p>";
}
?>
</body>
</html>