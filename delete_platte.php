<?php
require 'config.php';
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $stmt = $pdo->prepare("DELETE FROM schneidplatten WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    echo "OK";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Fehler: " . $e->getMessage();
}
