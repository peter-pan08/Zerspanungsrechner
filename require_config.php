<?php
$cfg = __DIR__ . '/config.php';
if (!file_exists($cfg)) {
    echo "Bitte install.php ausführen, um config.php anzulegen.";
    exit;
}
require_once $cfg;
?>
