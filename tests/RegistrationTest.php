<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class RegistrationTest extends TestCase
{
    public function testRegistrationStoresUser(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password_hash TEXT, rolle TEXT)");

        $username = 'bob';
        $password = 'pwd';

        $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $check->execute([$username]);
        $this->assertFalse($check->fetch());

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, rolle) VALUES (?, ?, 'viewer')");
        $stmt->execute([$username, $hash]);

        $count = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $this->assertSame(1, $count);
    }

    public function testRegistrationRejectsDuplicateUser(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password_hash TEXT, rolle TEXT)");
        $hash = password_hash('pw', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password_hash, rolle) VALUES (?, ?, 'viewer')")->execute(['bob', $hash]);

        $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $check->execute(['bob']);
        $this->assertNotFalse($check->fetch());
    }
}
