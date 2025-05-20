<?php
require 'config.php';
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    $stmt1 = $pdo->query("SELECT * FROM materialien ORDER BY name");
    $materialien = $stmt1->fetchAll();

    $stmt2 = $pdo->query("SELECT * FROM schneidplatten ORDER BY name");
    $platten = $stmt2->fetchAll();

    echo json_encode([
        'materialien' => $materialien,
        'platten' => $platten
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
