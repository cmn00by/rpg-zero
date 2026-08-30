<?php
namespace Models;

use Core\Database;
use PDO;

class Character {
    public static function getClasses(): array {
        $db = Database::getConnection();
        return $db->query("SELECT * FROM character_classes ORDER BY id ASC")->fetchAll();
    }

    public static function getClassById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM character_classes WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function findByUserId(int $userId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT c.*, cl.name AS class_name, cl.icon AS class_icon, cl.code AS class_code
            FROM characters c
            JOIN character_classes cl ON c.class_id = cl.id
            WHERE c.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute(['user_id' => $userId]);
        $char = $stmt->fetch();
        
        if ($char) {
            return self::applyPassiveRegen($char);
        }
        return null;
    }

    public static function findById(int $id): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT c.*, cl.name AS class_name, cl.icon AS class_icon, cl.code AS class_code
            FROM characters c
            JOIN character_classes cl ON c.class_id = cl.id
            WHERE c.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $id]);
        $char = $stmt->fetch();

        if ($char) {
            return self::applyPassiveRegen($char);
        }
        return null;
    }

    public static function create(int $userId, int $classId, string $name): int {
        $class = self::getClassById($classId);
        if (!$class) {
            throw new \Exception("Classe introuvable");
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO characters (
                user_id, class_id, name, level, xp, xp_next, gold,
                current_hp, max_hp, current_ap, max_ap,
                strength, agility, intelligence, last_activity
            ) VALUES (
                :user_id, :class_id, :name, 1, 0, 100, 50,
                :hp, :hp, :ap, :ap,
                :str, :agi, :int, NOW()
            )
        ");

        $stmt->execute([
            'user_id' => $userId,
            'class_id' => $classId,
            'name' => $name,
            'hp' => $class['base_hp'],
            'ap' => $class['base_ap'],
            'str' => $class['base_str'],
            'agi' => $class['base_agi'],
            'int' => $class['base_int'],
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Calcul de la régénération passive (années 2000 style, optimisé à la volée)
     * - 1 PA toutes les 60 secondes
     * - 1 PV toutes les 30 secondes
     */
    public static function applyPassiveRegen(array $char): array {
        $lastActivity = strtotime($char['last_activity']);
        $now = time();
        $secondsElapsed = max(0, $now - $lastActivity);

        if ($secondsElapsed < 10) {
            return $char;
        }

        $hpRegenRateSeconds = 30;
        $apRegenRateSeconds = 60;

        $hpGained = (int)floor($secondsElapsed / $hpRegenRateSeconds);
        $apGained = (int)floor($secondsElapsed / $apRegenRateSeconds);

        $newHp = min((int)$char['max_hp'], (int)$char['current_hp'] + $hpGained);
        $newAp = min((int)$char['max_ap'], (int)$char['current_ap'] + $apGained);

        if ($newHp !== (int)$char['current_hp'] || $newAp !== (int)$char['current_ap']) {
            $db = Database::getConnection();
            $stmt = $db->prepare("
                UPDATE characters 
                SET current_hp = :hp, current_ap = :ap, last_activity = NOW() 
                WHERE id = :id
            ");
            $stmt->execute([
                'hp' => $newHp,
                'ap' => $newAp,
                'id' => $char['id']
            ]);

            $char['current_hp'] = $newHp;
            $char['current_ap'] = $newAp;
            $char['last_activity'] = date('Y-m-d H:i:s');
        }

        return $char;
    }

    public static function updateHp(int $id, int $hp): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE characters SET current_hp = :hp, last_activity = NOW() WHERE id = :id");
        $stmt->execute(['hp' => max(0, $hp), 'id' => $id]);
    }

    public static function consumeAp(int $id, int $apCost): bool {
        $char = self::findById($id);
        if (!$char || $char['current_ap'] < $apCost) {
            return false;
        }

        $newAp = $char['current_ap'] - $apCost;
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE characters SET current_ap = :ap, last_activity = NOW() WHERE id = :id");
        $stmt->execute(['ap' => $newAp, 'id' => $id]);
        return true;
    }

    public static function healAtTavern(int $id, int $cost): bool {
        $char = self::findById($id);
        if (!$char || $char['gold'] < $cost) {
            return false;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE characters 
            SET current_hp = max_hp, current_ap = max_ap, gold = gold - :cost, last_activity = NOW() 
            WHERE id = :id
        ");
        $stmt->execute(['cost' => $cost, 'id' => $id]);
        return true;
    }

    public static function addXpAndGold(int $id, int $xpGain, int $goldGain): array {
        $char = self::findById($id);
        if (!$char) {
            return ['leveled_up' => false];
        }

        $newXp = $char['xp'] + $xpGain;
        $newGold = $char['gold'] + $goldGain;
        $level = $char['level'];
        $xpNext = $char['xp_next'];
        $maxHp = $char['max_hp'];
        $maxAp = $char['max_ap'];
        $strength = $char['strength'];
        $agility = $char['agility'];
        $intelligence = $char['intelligence'];
        $leveledUp = false;

        // Formule de montée de niveau
        while ($newXp >= $xpNext) {
            $newXp -= $xpNext;
            $level++;
            $xpNext = (int)floor($xpNext * 1.5);
            $maxHp += 15;
            $maxAp += 2;
            $strength += 2;
            $agility += 2;
            $intelligence += 2;
            $leveledUp = true;
        }

        $currentHp = $leveledUp ? $maxHp : $char['current_hp'];
        $currentAp = $leveledUp ? $maxAp : $char['current_ap'];

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE characters SET 
                level = :level,
                xp = :xp,
                xp_next = :xp_next,
                gold = :gold,
                max_hp = :max_hp,
                current_hp = :current_hp,
                max_ap = :max_ap,
                current_ap = :current_ap,
                strength = :strength,
                agility = :agility,
                intelligence = :intelligence,
                last_activity = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            'level' => $level,
            'xp' => $newXp,
            'xp_next' => $xpNext,
            'gold' => $newGold,
            'max_hp' => $maxHp,
            'current_hp' => $currentHp,
            'max_ap' => $maxAp,
            'current_ap' => $currentAp,
            'strength' => $strength,
            'agility' => $agility,
            'intelligence' => $intelligence,
            'id' => $id
        ]);

        return [
            'leveled_up' => $leveledUp,
            'new_level' => $level
        ];
    }
}
