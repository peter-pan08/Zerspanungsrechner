<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class LoginTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT, password_hash TEXT, rolle TEXT)");
        $hash = password_hash('secret', PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO users (username, password_hash, rolle) VALUES (?, ?, 'viewer')");
        $stmt->execute(['alice', $hash]);
    }

    public function testSuccessfulLogin(): void
    {
        $stmt = $this->pdo->prepare("SELECT id, password_hash, rolle FROM users WHERE username = ? LIMIT 1");
        $stmt->execute(['alice']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($row);
        $this->assertTrue(password_verify('secret', $row['password_hash']));
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $stmt = $this->pdo->prepare("SELECT id, password_hash, rolle FROM users WHERE username = ? LIMIT 1");
        $stmt->execute(['alice']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($row);
        $this->assertFalse(password_verify('wrong', $row['password_hash']));
    }
}
