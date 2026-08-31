<?php
namespace Models;

use Core\Database;
use PDO;

class Item {
    public static function getAll(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM items ORDER BY level_required ASC, type ASC")->fetchAll();
    }

    public static function getById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM items WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getByCode(string $code): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM items WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getRandomLootForLevel(int $monsterLevel): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT * FROM items 
            WHERE level_required <= :lvl 
            ORDER BY RAND() 
            LIMIT 1
        ");
        $stmt->execute(['lvl' => max(1, $monsterLevel)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }
}
