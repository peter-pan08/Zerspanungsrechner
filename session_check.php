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

// Fallback: Wenn LOGIN_REQUIRED nicht definiert ist, standardmäßig kein Login erzwingen
$__login_required = defined('LOGIN_REQUIRED') ? LOGIN_REQUIRED : false;

// Wenn Login erforderlich und kein Benutzer angemeldet, weiterleiten
if ($__login_required && empty($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}
