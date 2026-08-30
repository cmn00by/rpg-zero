<?php
namespace Models;

use Core\Database;
use PDO;

class User {
    public static function findByUsername(string $username): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(string $username, string $password): int {
        $db = Database::getConnection();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (:username, :hash)");
        $stmt->execute([
            'username' => $username,
            'hash' => $hash
        ]);
        return (int)$db->lastInsertId();
    }
}
