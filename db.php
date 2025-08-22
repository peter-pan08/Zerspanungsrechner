<?php
require_once __DIR__ . '/require_config.php';

function getPDO(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        global $host, $user, $pass, $db;
        $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
        try {
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            die('Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());
        }
    }
    return $pdo;
}
?>
