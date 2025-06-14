<?php
// session_check.php
// Stellt sicher, dass nur angemeldete Nutzer Zugriff haben
// und vermeidet doppelte session_start()-Aufrufe.

if (!defined('LOGIN_REQUIRED')) {
    require 'require_config.php';
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Wenn Login erforderlich und kein Benutzer angemeldet, weiterleiten
if (LOGIN_REQUIRED && empty($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
