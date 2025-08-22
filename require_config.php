<?php
$cfg = __DIR__ . '/config.php';
if (!file_exists($cfg)) {
    echo "Bitte install.php ausführen, um config.php anzulegen.";
    exit;
}
require_once $cfg;

if (defined('DEBUG') && DEBUG) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}
?>
