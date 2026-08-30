<?php
// Test E2E de logique de jeu RPG-Zero avec système de Niveaux & Stats
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Character.php';
require_once __DIR__ . '/../src/Models/Monster.php';
require_once __DIR__ . '/../src/Models/Battle.php';
require_once __DIR__ . '/../src/Models/Level.php';

use Models\User;
use Models\Character;
use Models\Monster;
use Models\Battle;
use Models\Level;

echo "=== 1. Test Table des Niveaux ===\n";
$allLevels = Level::getAll();
assert(count($allLevels) >= 20, "At least 20 levels configured");
$lvl2 = Level::getByLevel(2);
assert($lvl2 !== null, "Level 2 exists");
assert((int)$lvl2['xp_required'] === 80, "Level 2 requires 80 XP");
assert((int)$lvl2['stat_points_reward'] === 5, "Level 2 gives 5 stat points");
assert((int)$lvl2['gold_reward'] === 30, "Level 2 gives 30 bonus gold");
echo "✅ Table 'levels' opérationnelle (20 niveaux configurés, Niv. 2 = {$lvl2['title']})\n";

echo "=== 2. Test Inscription & Création de Personnage ===\n";
$testUsername = 'hero_' . time();
$userId = User::create($testUsername, 'password123');
$charName = 'Lancelot_' . rand(100, 999);
$charId = Character::create($userId, 1, $charName); // Guerrier
$char = Character::findById($charId);
assert($char !== null, "Character found");
assert($char['level'] == 1, "Level starts at 1");
assert($char['xp_next'] == 80, "Next level at 80 XP");
assert($char['stat_points'] == 0, "Initial stat points is 0");
assert($char['title'] === 'Novice', "Initial title is Novice");
echo "✅ Héros {$charName} créé : Niveau 1 (Titre: {$char['title']}, 0/{$char['xp_next']} XP)\n";

echo "=== 3. Test Montée de Niveau & Attribution de Récompenses ===\n";
// Attribution de 100 XP (dépasse 80 XP requis pour Niv. 2)
$rewards = Character::addXpAndGold($charId, 100, 20);
$char = Character::findById($charId);
assert($rewards['leveled_up'] === true, "Leveled up to 2");
assert($char['level'] == 2, "Character is now level 2");
assert($char['stat_points'] == 5, "Has 5 stat points to distribute");
assert($char['title'] === 'Aventurier débutant', "Title updated to Aventurier débutant");
assert($char['gold'] == (50 + 20 + 30), "Gold is 50 base + 20 monster + 30 level reward = 100");
echo "✅ Montée de Niveau 2 validée ! Titre: '{$char['title']}', Points de stats: {$char['stat_points']}, Or total: {$char['gold']} 💰\n";

echo "=== 4. Test Distribution des Points de Caractéristiques ===\n";
// Dépenser 2 points en Force
$res1 = Character::allocateStat($charId, 'strength');
assert($res1['success'] === true, "Allocated strength");
$res2 = Character::allocateStat($charId, 'strength');
assert($res2['success'] === true, "Allocated strength 2");

// Dépenser 1 point en Agilité
$res3 = Character::allocateStat($charId, 'agility');
assert($res3['success'] === true, "Allocated agility");

// Dépenser 1 point en PV Max (+10 PV)
$res4 = Character::allocateStat($charId, 'max_hp');
assert($res4['success'] === true, "Allocated max_hp");

// Dépenser 1 point en PA Max (+1 PA)
$res5 = Character::allocateStat($charId, 'max_ap');
assert($res5['success'] === true, "Allocated max_ap");

$char = Character::findById($charId);
assert($char['strength'] == 16, "Strength increased from 14 to 16");
assert($char['agility'] == 9, "Agility increased from 8 to 9");
assert($char['max_hp'] == 130, "Max HP increased from 120 to 130");
assert($char['max_ap'] == 16, "Max AP increased from 15 to 16");
assert($char['stat_points'] == 0, "Remaining stat points is now 0");

// Tenter de dépenser un point alors qu'il n'y en a plus
$res6 = Character::allocateStat($charId, 'strength');
assert($res6['success'] === false, "Cannot allocate without points");
echo "✅ Distribution de 5 points d'attributs validée (+2 Force, +1 Agi, +10 PV Max, +1 PA Max) !\n";

echo "\n✨ TOUS LES TESTS DE NIVEAUX ET CARACTÉRISTIQUES SONT VALIDÉS AVEC SUCCÈS !\n";
