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
        self::addLog($battleId, 1, 'system', 'start', 0, "Un sauvage **{$monster['name']}** {$monster['icon']} (Niv. {$monster['level']}) entre dans l'arène !");

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
     * Résolution complète du tour de combat
     */
    public static function processTurn(int $battleId, string $action): array {
        $battle = self::getById($battleId);
        if (!$battle || $battle['is_finished']) {
            return ['error' => 'Ce combat est déjà terminé.'];
        }

        $char = Character::getEffectiveStats((int)$battle['character_id']);
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
        $defenseStance = false;

        // 1. GESTION DES ACTIONS DU JOUEUR
        if ($action === 'flee') {
            $fleeChance = 50 + ($char['effective_agi'] - $battle['monster_agility']) * 3;
            $fleeChance = max(20, min(85, $fleeChance));
            $roll = rand(1, 100);

            if ($roll <= $fleeChance) {
                self::addLog($battleId, $turn, 'player', 'flee_success', 0, "💨 **FUITE RÉUSSIE !** Vous vous dégagez prestement de l'affrontement.");
                $isFinished = true;
                $winner = 'fled';
            } else {
                self::addLog($battleId, $turn, 'player', 'flee_fail', 0, "❌ **ÉCHEC DE LA FUITE !** Le monstre vous bloque le passage.");
            }
        } elseif ($action === 'potion') {
            // Boire une potion en combat
            $bag = Inventory::getBagItems((int)$char['id']);
            $potion = null;
            foreach ($bag as $item) {
                if ($item['type'] === 'consumable' && (int)$item['heal_hp'] > 0) {
                    $potion = $item;
                    break;
                }
            }

            if ($potion) {
                $consumeRes = Inventory::consumeItem((int)$char['id'], (int)$potion['character_item_id']);
                if ($consumeRes['success']) {
                    $playerHp = min((int)$char['effective_max_hp'], $playerHp + (int)$potion['heal_hp']);
                    self::addLog($battleId, $turn, 'player', 'potion', (int)$potion['heal_hp'], "🧪 **POTION !** Vous buvez {$potion['name']} et récupérez **+{$potion['heal_hp']} PV**.");
                } else {
                    self::addLog($battleId, $turn, 'player', 'potion_fail', 0, "❌ Impossible d'utiliser la potion.");
                }
            } else {
                self::addLog($battleId, $turn, 'player', 'potion_fail', 0, "⚠️ Vous n'avez aucune potion de soin dans votre sac !");
            }
        } elseif ($action === 'defend') {
            // Posture défensive
            $defenseStance = true;
            self::addLog($battleId, $turn, 'player', 'defend', 0, "🛡️ **POSTURE DÉFENSIVE !** Vous levez votre garde (Dégâts subis réduits de 50%).");
        } else {
            // Attaque standard OU Compétence de classe (special)
            $isSpecial = ($action === 'special');
            $hitRoll = rand(1, 100);
            $dodgeChance = max(5, min(30, (int)$battle['monster_agility'] - (int)$char['effective_agi']));

            if ($hitRoll <= $dodgeChance) {
                self::addLog($battleId, $turn, 'player', 'miss', 0, "💨 Le {$battle['monster_name']} esquive agilement votre assaut !");
            } else {
                $critBonus = $isSpecial && ($char['class_code'] ?? '') === 'rogue' ? 30 : 0;
                $critChance = 5 + (int)floor($char['effective_agi'] / 3) + $critBonus;
                $isCrit = rand(1, 100) <= $critChance;

                $classCode = $char['class_code'] ?? 'warrior';
                $mult = 1.0;
                $armorPen = 0.5;
                $skillName = "Frappe Directe";

                if ($isSpecial) {
                    if ($classCode === 'warrior') {
                        $skillName = "💥 Frappe Dévastatrice";
                        $mult = 1.45;
                        $armorPen = 0.25; // Ignore une partie de l'armure
                    } elseif ($classCode === 'rogue') {
                        $skillName = "🗡️ Attaque Sournoise";
                        $mult = 1.35;
                    } elseif ($classCode === 'mage') {
                        $skillName = "🔮 Éclair Arcanique";
                        $mult = 1.4;
                        $armorPen = 0.1; // Presque pur magique
                    }
                }

                $baseDmgRoll = rand((int)floor($char['total_attack'] * 0.85), (int)ceil($char['total_attack'] * 1.25));
                $damage = max(1, (int)floor($baseDmgRoll * $mult) - (int)floor($battle['monster_defense'] * $armorPen));

                if ($isCrit) {
                    $damage = (int)floor($damage * 1.8);
                    self::addLog($battleId, $turn, 'player', 'crit', $damage, "💥 **COUP CRITIQUE !** ({$skillName}) Vous infligez **{$damage}** dégâts !");
                } else {
                    $prefix = $isSpecial ? "✨ " : "⚔️ ";
                    self::addLog($battleId, $turn, 'player', 'hit', $damage, "{$prefix}**{$skillName} !** Vous infligez **{$damage}** dégâts au {$battle['monster_name']}.");
                }

                $monsterHp = max(0, $monsterHp - $damage);
            }

            // Vérifier mort du monstre
            if ($monsterHp <= 0) {
                $isFinished = true;
                $winner = 'player';
                
                $goldReward = rand((int)$battle['gold_reward_min'], (int)$battle['gold_reward_max']);
                $xpReward = (int)$battle['xp_reward'];

                $levelResult = Character::addXpAndGold($char['id'], $xpReward, $goldReward);
                $droppedItem = null;

                // Tirage de butin aléatoire (35% de chance)
                if (rand(1, 100) <= 35) {
                    $droppedItem = Item::getRandomLootForLevel((int)$battle['monster_level']);
                    if ($droppedItem) {
                        $addResult = Inventory::addItem((int)$char['id'], (int)$droppedItem['id'], 1);
                        if ($addResult['success']) {
                            self::addLog($battleId, $turn, 'system', 'loot', 0, "🎁 **BUTIN TROUVÉ !** Vous ramassez un(e) **{$droppedItem['name']}** {$droppedItem['icon']} !");
                        } else {
                            self::addLog($battleId, $turn, 'system', 'loot_full', 0, "⚠️ Le monstre a laissé tomber un(e) **{$droppedItem['name']}**, mais votre sac est plein !");
                        }
                    }
                }

                $rewards = [
                    'xp' => $xpReward,
                    'gold' => $goldReward,
                    'loot_item' => $droppedItem,
                    'leveled_up' => $levelResult['leveled_up'],
                    'new_level' => $levelResult['new_level'] ?? $char['level'],
                    'title' => $levelResult['title'] ?? $char['title'],
                    'stat_points_gained' => $levelResult['stat_points_gained'] ?? 0,
                    'gold_bonus_gained' => $levelResult['gold_bonus_gained'] ?? 0,
                    'slots_gained' => $levelResult['slots_gained'] ?? 0
                ];

                self::addLog($battleId, $turn, 'system', 'victory', 0, "🏆 **VICTOIRE ÉCLATANTE !** Le {$battle['monster_name']} est vaincu !");

                if ($levelResult['leveled_up']) {
                    self::addLog($battleId, $turn, 'system', 'levelup', 0, "✨ **MONTÉE DE NIVEAU !** Vous atteignez le **Niveau {$levelResult['new_level']}** (*{$levelResult['title']}*) ! Récompenses : +{$levelResult['gold_bonus_gained']} 💰 or, +{$levelResult['stat_points_gained']} attributs, +{$levelResult['slots_gained']} slot de sac !");
                }
            }
        }

        // 2. RIPOSTE DU MONSTRE (si combat actif)
        if (!$isFinished && $winner !== 'fled') {
            $monsterHitRoll = rand(1, 100);
            $playerDodgeChance = max(5, min(40, (int)$char['effective_agi'] - (int)$battle['monster_agility']));

            if ($monsterHitRoll <= $playerDodgeChance) {
                self::addLog($battleId, $turn, 'monster', 'miss', 0, "🛡️ Vous esquivez prestement la riposte du {$battle['monster_name']} !");
            } else {
                $monsterBaseDmg = rand((int)floor($battle['monster_attack'] * 0.7), (int)floor($battle['monster_attack'] * 1.2));
                $playerDef = (int)$char['total_defense'];
                
                $dmgCalc = max(1, $monsterBaseDmg - $playerDef);
                if ($defenseStance) {
                    $dmgCalc = max(1, (int)floor($dmgCalc * 0.5));
                }

                $playerHp = max(0, $playerHp - $dmgCalc);
                Character::updateHp($char['id'], $playerHp);

                $defNote = $defenseStance ? " (Garde levée : -50%)" : "";
                self::addLog($battleId, $turn, 'monster', 'hit', $dmgCalc, "🩸 Le {$battle['monster_name']} contre-attaque et inflige **{$dmgCalc}** dégâts{$defNote} !");

                if ($playerHp <= 0) {
                    $isFinished = true;
                    $winner = 'monster';
                    self::addLog($battleId, $turn, 'system', 'defeat', 0, "💀 **DÉFAITE...** Vos forces vous abandonnent. Vous êtes rapatrié à la taverne.");
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

        $updatedChar = Character::getEffectiveStats((int)$char['id']);
        $logs = self::getLogs($battleId);
        $summary = self::calculateDuelSummary($battleId, $battle, $updatedChar, $logs, $winner, $rewards);

        return [
            'battle' => self::getById($battleId),
            'character' => $updatedChar,
            'logs' => $logs,
            'is_finished' => $isFinished,
            'winner' => $winner,
            'rewards' => $rewards,
            'summary' => $summary
        ];
    }

    /**
     * Calcule le Compte-Rendu complet du duel (Style AnimeFight / VS Duel)
     */
    public static function calculateDuelSummary(int $battleId, array $battle, array $char, array $logs, ?string $winner, ?array $rewards): array {
        $playerDmgTotal = 0;
        $playerCritCount = 0;
        $playerDodges = 0;
        $monsterDmgTotal = 0;
        $monsterDodges = 0;

        $lastPlayerLog = null;
        $lastMonsterLog = null;

        foreach ($logs as $l) {
            if ($l['actor'] === 'player') {
                $playerDmgTotal += (int)$l['damage'];
                if ($l['action'] === 'crit') $playerCritCount++;
                if ($l['action'] === 'miss') $monsterDodges++;
                $lastPlayerLog = $l;
            } elseif ($l['actor'] === 'monster') {
                $monsterDmgTotal += (int)$l['damage'];
                if ($l['action'] === 'miss') $playerDodges++;
                $lastMonsterLog = $l;
            }
        }

        // Vérifier les potions dans le sac
        $bag = Inventory::getBagItems((int)$char['id']);
        $potionsCount = 0;
        foreach ($bag as $it) {
            if ($it['type'] === 'consumable' && (int)$it['heal_hp'] > 0) {
                $potionsCount += (int)$it['quantity'];
            }
        }

        $equipped = Inventory::getEquippedItems((int)$char['id']);

        return [
            'player_damage_total' => $playerDmgTotal,
            'player_damage_parried' => $playerDodges * 8 + ($char['total_defense'] * 2),
            'player_crits' => $playerCritCount,
            'player_score' => $playerDmgTotal * 10 + ($winner === 'player' ? 500 : 100),
            
            'monster_damage_total' => $monsterDmgTotal,
            'monster_damage_parried' => $monsterDodges * 6 + ((int)$battle['monster_defense'] * 2),
            'monster_score' => $monsterDmgTotal * 10 + ($winner === 'monster' ? 500 : 50),

            'last_player_action' => $lastPlayerLog,
            'last_monster_action' => $lastMonsterLog,
            'potions_count' => $potionsCount,
            'equipped' => $equipped
        ];
    }
}
