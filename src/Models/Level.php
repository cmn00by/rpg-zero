<?php
namespace Models;

use Core\Database;
use PDO;

class Level {
    public static function getAll(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM levels ORDER BY level ASC")->fetchAll();
    }

    public static function getByLevel(int $level): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM levels WHERE level = :level LIMIT 1");
        $stmt->execute(['level' => $level]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getNextLevel(int $currentLevel): ?array {
        return self::getByLevel($currentLevel + 1);
    }
}
