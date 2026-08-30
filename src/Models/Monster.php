<?php
namespace Models;

use Core\Database;
use PDO;

class Monster {
    public static function getAll(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM monsters ORDER BY level ASC")->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM monsters WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $monster = $stmt->fetch();
        return $monster ?: null;
    }

    public static function getRandomByZone(string $zone = 'forest'): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM monsters WHERE zone = :zone ORDER BY RAND() LIMIT 1");
        $stmt->execute(['zone' => $zone]);
        $monster = $stmt->fetch();
        return $monster ?: null;
    }
}
