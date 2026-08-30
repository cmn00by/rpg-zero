<?php
namespace Models;

use Core\Database;
use PDO;

class Battle {
    public static function getActiveBattle(int $characterId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT b.*, m.name AS monster_name, m.level AS monster_level, m.hp AS monster_max_hp,
                   m.attack AS monster_attack, m.defense AS monster_defense, m.agility AS monster_agility,
                   m.icon AS monster_icon, m.xp_reward, m.gold_reward_min, m.gold_reward_max
            FROM active_battles b
            JOIN monsters m ON b.monster_id = m.id
            WHERE b.character_id = :char_id AND b.is_finished = 0
            LIMIT 1
        ");
        $stmt->execute(['char_id' => $characterId]);
        $battle = $stmt->fetch();
        return $battle ?: null;
    }

    public static function getById(int $battleId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT b.*, m.name AS monster_name, m.level AS monster_level, m.hp AS monster_max_hp,
                   m.attack AS monster_attack, m.defense AS monster_defense, m.agility AS monster_agility,
                   m.icon AS monster_icon, m.xp_reward, m.gold_reward_min, m.gold_reward_max
            FROM active_battles b
            JOIN monsters m ON b.monster_id = m.id
            WHERE b.id = :id
            LIMIT 1
        ");
        $stmt->execute(['id' => $battleId]);
        $battle = $stmt->fetch();
        return $battle ?: null;
    }

    public static function create(int $characterId, int $monsterId): int {
        $monster = Monster::getById($monsterId);
        if (!$monster) {
            throw new \Exception("Monstre introuvable");
        }

        $db = Database::getConnection();
        // Clôturer tout ancien combat actif
        $stmt = $db->prepare("UPDATE active_battles SET is_finished = 1, winner = 'abandon' WHERE character_id = :char_id AND is_finished = 0");
        $stmt->execute(['char_id' => $characterId]);

        $stmt = $db->prepare("
            INSERT INTO active_battles (character_id, monster_id, monster_current_hp, turn, is_finished)
            VALUES (:char_id, :monster_id, :monster_hp, 1, 0)
        ");
        $stmt->execute([
            'char_id' => $characterId,
            'monster_id' => $monsterId,
            'monster_hp' => $monster['hp']
        ]);

        $battleId = (int)$db->lastInsertId();

        self::addLog($battleId, 1, 'system', 'start', 0, "Un sauvage **{$monster['name']}** {$monster['icon']} (Niv. {$monster['level']}) surgit des fourrés !");

        return $battleId;
    }

    public static function addLog(int $battleId, int $turn, string $actor, string $action, int $damage, string $message): void {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO battle_logs (battle_id, turn, actor, action, damage, message)
            VALUES (:battle_id, :turn, :actor, :action, :damage, :message)
        ");
        $stmt->execute([
            'battle_id' => $battleId,
            'turn' => $turn,
            'actor' => $actor,
            'action' => $action,
            'damage' => $damage,
            'message' => $message
        ]);
    }

    public static function getLogs(int $battleId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM battle_logs WHERE battle_id = :battle_id ORDER BY id ASC");
        $stmt->execute(['battle_id' => $battleId]);
        return $stmt->fetchAll();
    }

    /**
     * Résolution du tour de combat
     */
    public static function processTurn(int $battleId, string $action): array {
        $battle = self::getById($battleId);
        if (!$battle || $battle['is_finished']) {
            return ['error' => 'Ce combat est déjà terminé.'];
        }

        $char = Character::findById((int)$battle['character_id']);
        if (!$char) {
            return ['error' => 'Personnage introuvable.'];
        }

        $db = Database::getConnection();
        $turn = (int)$battle['turn'];
        $monsterHp = (int)$battle['monster_current_hp'];
        $playerHp = (int)$char['current_hp'];
        $isFinished = false;
        $winner = null;
        $rewards = null;

        if ($action === 'flee') {
            // Chance de fuite basée sur l'agilité
            $fleeChance = 50 + ($char['agility'] - $battle['monster_agility']) * 3;
            $fleeChance = max(20, min(85, $fleeChance));
            $roll = rand(1, 100);

            if ($roll <= $fleeChance) {
                self::addLog($battleId, $turn, 'player', 'flee_success', 0, "💨 Vous prenez vos jambes à votre cou et parvenez à fuir !");
                $isFinished = true;
                $winner = 'fled';
            } else {
                self::addLog($battleId, $turn, 'player', 'flee_fail', 0, "❌ Vous trébuchez en tentant de fuir !");
            }
        } elseif ($action === 'attack') {
            // 1. Attaque du joueur
            $hitRoll = rand(1, 100);
            $dodgeChance = max(5, min(30, (int)$battle['monster_agility'] - (int)$char['agility']));

            if ($hitRoll <= $dodgeChance) {
                self::addLog($battleId, $turn, 'player', 'miss', 0, "💨 Le {$battle['monster_name']} esquive agilement votre assaut !");
            } else {
                $critChance = 5 + (int)floor($char['agility'] / 3);
                $isCrit = rand(1, 100) <= $critChance;

                // Formule de dégâts selon la classe
                if ($char['class_code'] === 'mage') {
                    $baseDamage = rand((int)floor($char['intelligence'] * 0.8), (int)floor($char['intelligence'] * 1.4));
                } elseif ($char['class_code'] === 'rogue') {
                    $baseDamage = rand((int)floor($char['agility'] * 0.7), (int)floor($char['agility'] * 1.3));
                } else {
                    $baseDamage = rand((int)floor($char['strength'] * 0.7), (int)floor($char['strength'] * 1.3));
                }

                $damage = max(1, $baseDamage - (int)floor($battle['monster_defense'] * 0.5));
                if ($isCrit) {
                    $damage = (int)floor($damage * 1.8);
                    self::addLog($battleId, $turn, 'player', 'crit', $damage, "💥 **COUP CRITIQUE !** Vous infligez **{$damage}** dégâts au {$battle['monster_name']} !");
                } else {
                    self::addLog($battleId, $turn, 'player', 'hit', $damage, "⚔️ Vous frappez le {$battle['monster_name']} et lui infligez **{$damage}** dégâts.");
                }

                $monsterHp = max(0, $monsterHp - $damage);
            }

            // Vérifier si le monstre est mort
            if ($monsterHp <= 0) {
                $isFinished = true;
                $winner = 'player';
                
                $goldReward = rand((int)$battle['gold_reward_min'], (int)$battle['gold_reward_max']);
                $xpReward = (int)$battle['xp_reward'];

                $levelResult = Character::addXpAndGold($char['id'], $xpReward, $goldReward);
                $rewards = [
                    'xp' => $xpReward,
                    'gold' => $goldReward,
                    'leveled_up' => $levelResult['leveled_up'],
                    'new_level' => $levelResult['new_level'] ?? $char['level'],
                    'title' => $levelResult['title'] ?? $char['title'],
                    'stat_points_gained' => $levelResult['stat_points_gained'] ?? 0,
                    'gold_bonus_gained' => $levelResult['gold_bonus_gained'] ?? 0
                ];

                self::addLog($battleId, $turn, 'system', 'victory', 0, "🏆 **VICTOIRE !** Le {$battle['monster_name']} s'effondre. Vous gagnez **+{$xpReward} XP** et **+{$goldReward} pièces d'or** !");
                if ($levelResult['leveled_up']) {
                    self::addLog($battleId, $turn, 'system', 'levelup', 0, "✨ **FÉLICITATIONS !** Vous atteignez le **Niveau {$levelResult['new_level']}** (Titre : *{$levelResult['title']}*) ! Récompenses : **+{$levelResult['gold_bonus_gained']} 💰 or**, **+{$levelResult['stat_points_gained']} points d'attributs** à répartir sur votre fiche de héros !");
                }
            }
        }

        // 2. Riposte du monstre si le combat continue
        if (!$isFinished && $action !== 'flee_success') {
            $monsterHitRoll = rand(1, 100);
            $playerDodgeChance = max(5, min(40, (int)$char['agility'] - (int)$battle['monster_agility']));

            if ($monsterHitRoll <= $playerDodgeChance) {
                self::addLog($battleId, $turn, 'monster', 'miss', 0, "🛡️ Vous esquivez prestement la riposte du {$battle['monster_name']} !");
            } else {
                $monsterBaseDmg = rand((int)floor($battle['monster_attack'] * 0.7), (int)floor($battle['monster_attack'] * 1.2));
                $playerDef = (int)floor($char['strength'] * 0.4);
                $monsterDmg = max(1, $monsterBaseDmg - $playerDef);

                $playerHp = max(0, $playerHp - $monsterDmg);
                Character::updateHp($char['id'], $playerHp);

                self::addLog($battleId, $turn, 'monster', 'hit', $monsterDmg, "🩸 Le {$battle['monster_name']} vous attaque et vous inflige **{$monsterDmg}** points de dégâts !");

                if ($playerHp <= 0) {
                    $isFinished = true;
                    $winner = 'monster';
                    self::addLog($battleId, $turn, 'system', 'defeat', 0, "💀 **DÉFAITE...** Vous succombez sous les coups du monstre. Vous êtes rapatrié inconscient à la taverne.");
                }
            }
        }

        // Mettre à jour l'état du combat
        $turn++;
        $stmt = $db->prepare("
            UPDATE active_battles 
            SET monster_current_hp = :m_hp, turn = :turn, is_finished = :is_finished, winner = :winner
            WHERE id = :id
        ");
        $stmt->execute([
            'm_hp' => $monsterHp,
            'turn' => $turn,
            'is_finished' => $isFinished ? 1 : 0,
            'winner' => $winner,
            'id' => $battleId
        ]);

        return [
            'battle' => self::getById($battleId),
            'character' => Character::findById($char['id']),
            'logs' => self::getLogs($battleId),
            'is_finished' => $isFinished,
            'winner' => $winner,
            'rewards' => $rewards
        ];
    }
}
