<?php
require 'config.php';
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if (!empty($_POST['id'])) {
        // UPDATE
        $stmt = $pdo->prepare("UPDATE schneidplatten SET name=?, typ=?, material=?, vc=?, gruppen=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['typ'],
            $_POST['material'],
            $_POST['vc'],
            $_POST['gruppen'],
            $_POST['id']
        ]);
    } else {
        // INSERT
        $stmt = $pdo->prepare("INSERT INTO schneidplatten (name, typ, material, vc, gruppen) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['typ'],
            $_POST['material'],
            $_POST['vc'],
            $_POST['gruppen']
        ]);
    }

    echo "OK";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Fehler: " . $e->getMessage();
}
