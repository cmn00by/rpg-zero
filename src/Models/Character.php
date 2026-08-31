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

    public static function getEffectiveStats(int $characterId): array {
        $char = self::findById($characterId);
        if (!$char) {
            return [];
        }

        $bonus = Inventory::getEquippedBonusTotals($characterId);

        $effStr = (int)$char['strength'] + $bonus['bonus_str'];
        $effAgi = (int)$char['agility'] + $bonus['bonus_agi'];
        $effInt = (int)$char['intelligence'] + $bonus['bonus_int'];
        $effMaxHp = (int)$char['max_hp'] + $bonus['bonus_hp'];
        $effMaxAp = (int)$char['max_ap'] + $bonus['bonus_ap'];

        $baseDef = (int)floor($effStr * 0.4);
        $totalDef = $baseDef + $bonus['bonus_defense'];

        // Base damage calculation by class
        if ($char['class_code'] === 'mage') {
            $baseAtk = (int)floor($effInt * 1.1);
        } elseif ($char['class_code'] === 'rogue') {
            $baseAtk = (int)floor($effAgi * 1.0);
        } else {
            $baseAtk = (int)floor($effStr * 1.0);
        }
        $totalAtk = $baseAtk + $bonus['bonus_attack'];

        return array_merge($char, [
            'effective_str' => $effStr,
            'effective_agi' => $effAgi,
            'effective_int' => $effInt,
            'effective_max_hp' => $effMaxHp,
            'effective_max_ap' => $effMaxAp,
            'bonus_attack' => $bonus['bonus_attack'],
            'bonus_defense' => $bonus['bonus_defense'],
            'total_attack' => $totalAtk,
            'total_defense' => $totalDef,
            'equipped_bonuses' => $bonus
        ]);
    }

    public static function create(int $userId, int $classId, string $name): int {
        $class = self::getClassById($classId);
        if (!$class) {
            throw new \Exception("Classe introuvable");
        }

        $level2 = Level::getByLevel(2);
        $xpNext = $level2 ? (int)$level2['xp_required'] : 80;

        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO characters (
                user_id, class_id, name, title, level, xp, xp_next, gold, stat_points, inventory_slots,
                current_hp, max_hp, current_ap, max_ap,
                strength, agility, intelligence, last_activity
            ) VALUES (
                :user_id, :class_id, :name, 'Novice', 1, 0, :xp_next, 50, 0, 10,
                :current_hp, :max_hp, :current_ap, :max_ap,
                :str, :agi, :int, NOW()
            )
        ");

        $stmt->execute([
            'user_id' => $userId,
            'class_id' => $classId,
            'name' => $name,
            'xp_next' => $xpNext,
            'current_hp' => $class['base_hp'],
            'max_hp' => $class['base_hp'],
            'current_ap' => $class['base_ap'],
            'max_ap' => $class['base_ap'],
            'str' => $class['base_str'],
            'agi' => $class['base_agi'],
            'int' => $class['base_int'],
        ]);

        $charId = (int)$db->lastInsertId();

        // Équipement de départ selon la classe
        $startWeaponCode = match ($class['code']) {
            'mage' => 'apprentice_staff',
            'rogue' => 'steel_dagger',
            default => 'iron_sword',
        };

        $startWeapon = Item::getByCode($startWeaponCode);
        if ($startWeapon) {
            $stmt = $db->prepare("INSERT INTO character_items (character_id, item_id, is_equipped, slot_position, quantity) VALUES (:cid, :iid, 1, 'weapon', 1)");
            $stmt->execute(['cid' => $charId, 'iid' => $startWeapon['id']]);
        }

        $startRobe = Item::getByCode('tattered_robe');
        if ($startRobe) {
            $stmt = $db->prepare("INSERT INTO character_items (character_id, item_id, is_equipped, slot_position, quantity) VALUES (:cid, :iid, 1, 'chest', 1)");
            $stmt->execute(['cid' => $charId, 'iid' => $startRobe['id']]);
        }

        // Potions de départ dans le sac
        $potion = Item::getByCode('health_potion_minor');
        if ($potion) {
            Inventory::addItem($charId, (int)$potion['id'], 2);
        }

        return $charId;
    }

    /**
     * Calcul de la régénération passive (années 2000 style, optimisé à la volée)
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
        $level = (int)$char['level'];
        $xpNext = (int)$char['xp_next'];
        $statPoints = (int)$char['stat_points'];
        $invSlots = (int)$char['inventory_slots'];
        $title = $char['title'];
        $leveledUp = false;
        $statPointsGained = 0;
        $goldBonusGained = 0;
        $slotsGained = 0;

        // Gestion du passage de niveau via la table 'levels'
        while ($newXp >= $xpNext) {
            $newXp -= $xpNext;
            $level++;
            $nextLvlConfig = Level::getByLevel($level);

            if ($nextLvlConfig) {
                $statPointsReward = (int)$nextLvlConfig['stat_points_reward'];
                $goldReward = (int)$nextLvlConfig['gold_reward'];
                $slotsReward = (int)$nextLvlConfig['inventory_slots_reward'];
                $title = $nextLvlConfig['title'];
                
                $statPoints += $statPointsReward;
                $newGold += $goldReward;
                $invSlots += $slotsReward;
                
                $statPointsGained += $statPointsReward;
                $goldBonusGained += $goldReward;
                $slotsGained += $slotsReward;
            } else {
                $statPoints += 5;
                $newGold += 100;
                $invSlots += 1;
                $statPointsGained += 5;
                $goldBonusGained += 100;
                $slotsGained += 1;
            }

            // Calcul du palier d'XP suivant
            $afterNextLvlConfig = Level::getByLevel($level + 1);
            if ($afterNextLvlConfig) {
                $xpNext = (int)$afterNextLvlConfig['xp_required'];
            } else {
                $xpNext = (int)floor($xpNext * 1.4);
            }

            $leveledUp = true;
        }

        $currentHp = $leveledUp ? $char['max_hp'] : $char['current_hp'];
        $currentAp = $leveledUp ? $char['max_ap'] : $char['current_ap'];

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE characters SET 
                level = :level,
                title = :title,
                xp = :xp,
                xp_next = :xp_next,
                gold = :gold,
                stat_points = :stat_points,
                inventory_slots = :inv_slots,
                current_hp = :current_hp,
                current_ap = :current_ap,
                last_activity = NOW()
            WHERE id = :id
        ");

        $stmt->execute([
            'level' => $level,
            'title' => $title,
            'xp' => $newXp,
            'xp_next' => $xpNext,
            'gold' => $newGold,
            'stat_points' => $statPoints,
            'inv_slots' => $invSlots,
            'current_hp' => $currentHp,
            'current_ap' => $currentAp,
            'id' => $id
        ]);

        return [
            'leveled_up' => $leveledUp,
            'new_level' => $level,
            'title' => $title,
            'stat_points_gained' => $statPointsGained,
            'gold_bonus_gained' => $goldBonusGained,
            'slots_gained' => $slotsGained
        ];
    }

    public static function allocateStat(int $id, string $stat): array {
        $char = self::findById($id);
        if (!$char || (int)$char['stat_points'] <= 0) {
            return ['success' => false, 'error' => 'Aucun point de caractéristique disponible.'];
        }

        $allowedStats = ['strength', 'agility', 'intelligence', 'max_hp', 'max_ap'];
        if (!in_array($stat, $allowedStats, true)) {
            return ['success' => false, 'error' => 'Caractéristique invalide.'];
        }

        $db = Database::getConnection();

        if ($stat === 'max_hp') {
            $stmt = $db->prepare("
                UPDATE characters 
                SET max_hp = max_hp + 10, current_hp = current_hp + 10, stat_points = stat_points - 1, last_activity = NOW()
                WHERE id = :id AND stat_points > 0
            ");
        } elseif ($stat === 'max_ap') {
            $stmt = $db->prepare("
                UPDATE characters 
                SET max_ap = max_ap + 1, current_ap = current_ap + 1, stat_points = stat_points - 1, last_activity = NOW()
                WHERE id = :id AND stat_points > 0
            ");
        } else {
            $stmt = $db->prepare("
                UPDATE characters 
                SET {$stat} = {$stat} + 1, stat_points = stat_points - 1, last_activity = NOW()
                WHERE id = :id AND stat_points > 0
            ");
        }

        $stmt->execute(['id' => $id]);

        $updatedChar = self::findById($id);
        return [
            'success' => true,
            'character' => $updatedChar
        ];
    }
}
