<?php
// session_check.php
// Stellt sicher, dass nur angemeldete Nutzer Zugriff haben
// und vermeidet doppelte session_start()-Aufrufe.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Wenn kein Login, zurück zur Login-Seite
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
