<?php
// Test E2E de logique de jeu RPG-Zero
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Character.php';
require_once __DIR__ . '/../src/Models/Monster.php';
require_once __DIR__ . '/../src/Models/Battle.php';

use Models\User;
use Models\Character;
use Models\Monster;
use Models\Battle;

echo "=== 1. Test Inscription Utilisateur ===\n";
$testUsername = 'hero_' . time();
$userId = User::create($testUsername, 'password123');
$user = User::findById($userId);
assert($user !== null, "User must be found");
assert(password_verify('password123', $user['password_hash']), "Password hash must be valid");
echo "✅ Utilisateur {$testUsername} créé avec ID {$userId}\n";

echo "=== 2. Test Création de Personnage (Guerrier) ===\n";
$charName = 'Galahad_' . rand(100, 999);
$charId = Character::create($userId, 1, $charName);
$character = Character::findById($charId);
assert($character !== null, "Character must be found");
assert($character['name'] === $charName, "Character name match");
assert($character['current_hp'] === 120, "Warrior HP is 120");
assert($character['current_ap'] === 15, "Warrior AP is 15");
echo "✅ Héros {$charName} créé avec 120 PV et 15 PA\n";

echo "=== 3. Test Consommation PA & Taverne ===\n";
$consumed = Character::consumeAp($charId, 5);
assert($consumed === true, "AP consumed successfully");
$character = Character::findById($charId);
assert($character['current_ap'] === 10, "AP reduced to 10");

Character::updateHp($charId, 50);
$character = Character::findById($charId);
assert($character['current_hp'] === 50, "HP reduced to 50");

$healed = Character::healAtTavern($charId, 10);
assert($healed === true, "Healed at tavern");
$character = Character::findById($charId);
assert($character['current_hp'] === 120, "HP fully restored to 120");
assert($character['current_ap'] === 15, "AP fully restored to 15");
assert($character['gold'] === 40, "Gold decreased from 50 to 40");
echo "✅ Soin à la Taverne validé (PV 120, PA 15, Or 40)\n";

echo "=== 4. Test Combat contre un Gobelin ===\n";
$monster = Monster::getById(2); // Gobelin pillard
assert($monster !== null, "Monster found");
$battleId = Battle::create($charId, $monster['id']);
$battle = Battle::getById($battleId);
assert($battle !== null, "Battle started");
echo "⚔️ Combat #{$battleId} initié contre {$monster['name']} ({$monster['hp']} PV)\n";

// Tour d'attaque
$round = 1;
while (!$battle['is_finished'] && $round <= 10) {
    $result = Battle::processTurn($battleId, 'attack');
    $battle = $result['battle'];
    $char = $result['character'];
    echo "  -> Tour {$round} terminé : PV Héros = {$char['current_hp']}, PV Monstre = {$battle['monster_current_hp']}\n";
    $round++;
}

assert($battle['is_finished'] == 1, "Battle must finish");
assert($battle['winner'] === 'player', "Player should defeat goblin");
$logs = Battle::getLogs($battleId);
assert(count($logs) > 0, "Logs recorded");
echo "🏆 Victoire du joueur après " . ($round - 1) . " tours ! Total de " . count($logs) . " logs enregistrés.\n";

$finalChar = Character::findById($charId);
echo "📊 Stats après combat : Niveau {$finalChar['level']}, XP: {$finalChar['xp']}, Or: {$finalChar['gold']}\n";

echo "\n✨ TOUS LES TESTS DE LOGIQUE DE JEU SONT VALIDÉS AVEC SUCCÈS !\n";
