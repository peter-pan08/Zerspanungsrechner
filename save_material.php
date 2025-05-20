<?php
require 'config.php';
try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    if (!empty($_POST['id'])) {
        // UPDATE
        $stmt = $pdo->prepare("UPDATE materialien SET name=?, typ=?, gruppe=?, vc_hss=?, vc_hartmetall=?, kc=? WHERE id=?");
        $stmt->execute([
            $_POST['name'],
            $_POST['typ'],
            $_POST['gruppe'],
            $_POST['vc_hss'],
            $_POST['vc_hartmetall'],
            $_POST['kc'],
            $_POST['id']
        ]);
    } else {
        // INSERT
        $stmt = $pdo->prepare("INSERT INTO materialien (name, typ, gruppe, vc_hss, vc_hartmetall, kc) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['name'],
            $_POST['typ'],
            $_POST['gruppe'],
            $_POST['vc_hss'],
            $_POST['vc_hartmetall'],
            $_POST['kc']
        ]);
    }

    echo "OK";
} catch (PDOException $e) {
    http_response_code(500);
    echo "Fehler: " . $e->getMessage();
}
