<?php
namespace Models;

use Core\Database;
use PDO;

class Inventory {
    public static function getBagItems(int $characterId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ci.id AS character_item_id, ci.quantity, ci.is_equipped, ci.slot_position,
                   i.*
            FROM character_items ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = :char_id AND ci.is_equipped = 0
            ORDER BY i.type ASC, i.level_required ASC, i.id ASC
        ");
        $stmt->execute(['char_id' => $characterId]);
        return $stmt->fetchAll();
    }

    public static function getEquippedItems(int $characterId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ci.id AS character_item_id, ci.slot_position,
                   i.*
            FROM character_items ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = :char_id AND ci.is_equipped = 1
        ");
        $stmt->execute(['char_id' => $characterId]);
        $rows = $stmt->fetchAll();

        $equipped = [
            'head' => null,
            'chest' => null,
            'boots' => null,
            'weapon' => null,
            'shield' => null,
            'ring' => null
        ];

        foreach ($rows as $row) {
            $slot = $row['slot_position'];
            if (array_key_exists($slot, $equipped)) {
                $equipped[$slot] = $row;
            }
        }

        return $equipped;
    }

    public static function getEquippedBonusTotals(int $characterId): array {
        $equipped = self::getEquippedItems($characterId);
        $totals = [
            'bonus_attack' => 0,
            'bonus_defense' => 0,
            'bonus_str' => 0,
            'bonus_agi' => 0,
            'bonus_int' => 0,
            'bonus_hp' => 0,
            'bonus_ap' => 0,
        ];

        foreach ($equipped as $item) {
            if ($item !== null) {
                $totals['bonus_attack'] += (int)$item['bonus_attack'];
                $totals['bonus_defense'] += (int)$item['bonus_defense'];
                $totals['bonus_str'] += (int)$item['bonus_str'];
                $totals['bonus_agi'] += (int)$item['bonus_agi'];
                $totals['bonus_int'] += (int)$item['bonus_int'];
                $totals['bonus_hp'] += (int)$item['bonus_hp'];
                $totals['bonus_ap'] += (int)$item['bonus_ap'];
            }
        }

        return $totals;
    }

    public static function addItem(int $characterId, int $itemId, int $quantity = 1): array {
        $item = Item::getById($itemId);
        if (!$item) {
            return ['success' => false, 'error' => 'Objet inexistant.'];
        }

        $char = Character::findById($characterId);
        if (!$char) {
            return ['success' => false, 'error' => 'Personnage introuvable.'];
        }

        $db = Database::getConnection();

        // 1. Si consommable : vérifier si déjà présent dans le sac pour l'empiler
        if ($item['type'] === 'consumable') {
            $stmt = $db->prepare("
                SELECT id, quantity FROM character_items 
                WHERE character_id = :char_id AND item_id = :item_id AND is_equipped = 0 
                LIMIT 1
            ");
            $stmt->execute(['char_id' => $characterId, 'item_id' => $itemId]);
            $existing = $stmt->fetch();

            if ($existing) {
                $newQty = $existing['quantity'] + $quantity;
                $stmt = $db->prepare("UPDATE character_items SET quantity = :qty WHERE id = :id");
                $stmt->execute(['qty' => $newQty, 'id' => $existing['id']]);
                return ['success' => true, 'stacked' => true, 'item' => $item];
            }
        }

        // 2. Vérification de la capacité du sac
        $bagItems = self::getBagItems($characterId);
        $maxSlots = (int)$char['inventory_slots'];

        if (count($bagItems) >= $maxSlots) {
            return ['success' => false, 'error' => "Votre sac est plein ({$maxSlots} emplacements maximum) !"];
        }

        $stmt = $db->prepare("
            INSERT INTO character_items (character_id, item_id, is_equipped, slot_position, quantity)
            VALUES (:char_id, :item_id, 0, NULL, :quantity)
        ");
        $stmt->execute([
            'char_id' => $characterId,
            'item_id' => $itemId,
            'quantity' => $quantity
        ]);

        return ['success' => true, 'stacked' => false, 'item' => $item];
    }

    public static function equipItem(int $characterId, int $characterItemId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ci.*, i.type, i.level_required, i.name, i.bonus_hp, i.bonus_ap
            FROM character_items ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = :id AND ci.character_id = :char_id AND ci.is_equipped = 0
            LIMIT 1
        ");
        $stmt->execute(['id' => $characterItemId, 'char_id' => $characterId]);
        $entry = $stmt->fetch();

        if (!$entry) {
            return ['success' => false, 'error' => 'Objet introuvable dans votre sac.'];
        }

        if ($entry['type'] === 'consumable') {
            return ['success' => false, 'error' => 'Cet objet est un consommable, il ne peut être équipé.'];
        }

        $char = Character::findById($characterId);
        if ($char['level'] < $entry['level_required']) {
            return ['success' => false, 'error' => "Niveau {$entry['level_required']} requis pour équiper {$entry['name']}."];
        }

        $slot = $entry['type'];

        // Si une pièce est déjà équipée dans ce slot, la remettre dans le sac
        $stmt = $db->prepare("
            UPDATE character_items 
            SET is_equipped = 0, slot_position = NULL 
            WHERE character_id = :char_id AND slot_position = :slot AND is_equipped = 1
        ");
        $stmt->execute(['char_id' => $characterId, 'slot' => $slot]);

        // Équiper la nouvelle pièce
        $stmt = $db->prepare("
            UPDATE character_items 
            SET is_equipped = 1, slot_position = :slot 
            WHERE id = :id
        ");
        $stmt->execute(['slot' => $slot, 'id' => $characterItemId]);

        // Booster les PV et PA actuels si la pièce confère un bonus
        $bonusHp = (int)$entry['bonus_hp'];
        $bonusAp = (int)$entry['bonus_ap'];
        if ($bonusHp > 0 || $bonusAp > 0) {
            $stmt = $db->prepare("UPDATE characters SET current_hp = current_hp + :hp, current_ap = current_ap + :ap, last_activity = NOW() WHERE id = :id");
            $stmt->execute(['hp' => $bonusHp, 'ap' => $bonusAp, 'id' => $characterId]);
        }

        return ['success' => true, 'message' => "Vous avez équipé : {$entry['name']}."];
    }

    public static function unequipItem(int $characterId, string $slot): array {
        $char = Character::findById($characterId);
        $bagItems = self::getBagItems($characterId);
        if (count($bagItems) >= (int)$char['inventory_slots']) {
            return ['success' => false, 'error' => 'Votre sac est plein, impossible de déséquiper cet objet.'];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ci.*, i.name 
            FROM character_items ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.character_id = :char_id AND ci.slot_position = :slot AND ci.is_equipped = 1
            LIMIT 1
        ");
        $stmt->execute(['char_id' => $characterId, 'slot' => $slot]);
        $equipped = $stmt->fetch();

        if (!$equipped) {
            return ['success' => false, 'error' => 'Aucun objet équipé dans cet emplacement.'];
        }

        $stmt = $db->prepare("
            UPDATE character_items 
            SET is_equipped = 0, slot_position = NULL 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $equipped['id']]);

        // Ajuster si les PV/PA actuels dépassent le nouveau max effectif
        $eff = Character::getEffectiveStats($characterId);
        $clampedHp = min((int)$eff['effective_max_hp'], (int)$eff['current_hp']);
        $clampedAp = min((int)$eff['effective_max_ap'], (int)$eff['current_ap']);
        if ($clampedHp !== (int)$eff['current_hp'] || $clampedAp !== (int)$eff['current_ap']) {
            $stmt = $db->prepare("UPDATE characters SET current_hp = :hp, current_ap = :ap, last_activity = NOW() WHERE id = :id");
            $stmt->execute(['hp' => $clampedHp, 'ap' => $clampedAp, 'id' => $characterId]);
        }

        return ['success' => true, 'message' => "Vous avez rangé {$equipped['name']} dans votre sac."];
    }

    public static function consumeItem(int $characterId, int $characterItemId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ci.*, i.name, i.type, i.heal_hp, i.restore_ap
            FROM character_items ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = :id AND ci.character_id = :char_id AND ci.is_equipped = 0
            LIMIT 1
        ");
        $stmt->execute(['id' => $characterItemId, 'char_id' => $characterId]);
        $entry = $stmt->fetch();

        if (!$entry || $entry['type'] !== 'consumable') {
            return ['success' => false, 'error' => 'Consommable introuvable.'];
        }

        $eff = Character::getEffectiveStats($characterId);
        $healHp = (int)$entry['heal_hp'];
        $restoreAp = (int)$entry['restore_ap'];

        $newHp = min((int)$eff['effective_max_hp'], (int)$eff['current_hp'] + $healHp);
        $newAp = min((int)$eff['effective_max_ap'], (int)$eff['current_ap'] + $restoreAp);

        $stmt = $db->prepare("UPDATE characters SET current_hp = :hp, current_ap = :ap, last_activity = NOW() WHERE id = :id");
        $stmt->execute(['hp' => $newHp, 'ap' => $newAp, 'id' => $characterId]);

        // Décrémenter quantité ou supprimer
        if ($entry['quantity'] > 1) {
            $stmt = $db->prepare("UPDATE character_items SET quantity = quantity - 1 WHERE id = :id");
            $stmt->execute(['id' => $characterItemId]);
        } else {
            $stmt = $db->prepare("DELETE FROM character_items WHERE id = :id");
            $stmt->execute(['id' => $characterItemId]);
        }

        $msgParts = [];
        if ($healHp > 0) $msgParts[] = "+{$healHp} PV";
        if ($restoreAp > 0) $msgParts[] = "+{$restoreAp} PA";

        return [
            'success' => true,
            'message' => "Vous utilisez {$entry['name']} (" . implode(', ', $msgParts) . ") !"
        ];
    }

    public static function sellItem(int $characterId, int $characterItemId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT ci.*, i.name, i.sell_price 
            FROM character_items ci
            JOIN items i ON ci.item_id = i.id
            WHERE ci.id = :id AND ci.character_id = :char_id AND ci.is_equipped = 0
            LIMIT 1
        ");
        $stmt->execute(['id' => $characterItemId, 'char_id' => $characterId]);
        $entry = $stmt->fetch();

        if (!$entry) {
            return ['success' => false, 'error' => 'Objet introuvable.'];
        }

        $totalGoldGain = (int)$entry['sell_price'] * (int)$entry['quantity'];

        $stmt = $db->prepare("UPDATE characters SET gold = gold + :gain, last_activity = NOW() WHERE id = :id");
        $stmt->execute(['gain' => $totalGoldGain, 'id' => $characterId]);

        $stmt = $db->prepare("DELETE FROM character_items WHERE id = :id");
        $stmt->execute(['id' => $characterItemId]);

        return [
            'success' => true,
            'message' => "Vous avez vendu {$entry['name']} pour {$totalGoldGain} pièces d'or 💰."
        ];
    }
}
