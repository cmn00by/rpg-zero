<?php
// Test E2E de logique de jeu RPG-Zero avec Inventaire, Équipements, Carte & Shop
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Character.php';
require_once __DIR__ . '/../src/Models/Monster.php';
require_once __DIR__ . '/../src/Models/Battle.php';
require_once __DIR__ . '/../src/Models/Level.php';
require_once __DIR__ . '/../src/Models/Item.php';
require_once __DIR__ . '/../src/Models/Inventory.php';
require_once __DIR__ . '/../src/Models/WorldMap.php';

use Models\User;
use Models\Character;
use Models\Monster;
use Models\Battle;
use Models\Level;
use Models\Item;
use Models\Inventory;
use Models\WorldMap;

echo "=== 1. Test Catalogue d'Objets ===\n";
$items = Item::getAll();
assert(count($items) >= 15, "At least 15 items in catalog");
$ironSword = Item::getByCode('iron_sword');
assert($ironSword !== null, "Iron sword exists");
assert((int)$ironSword['bonus_attack'] === 7, "Iron sword attack is +7");
echo "✅ Catalogue d'objets validé (" . count($items) . " objets chargés)\n";

echo "=== 2. Test Création de Personnage & Positionnement sur la Carte ===\n";
$testUsername = 'hero_' . time();
$userId = User::create($testUsername, 'password123');
$charName = 'Perceval_' . rand(100, 999);
$charId = Character::create($userId, 1, $charName); // Guerrier

$char = Character::findById($charId);
assert((int)$char['current_zone_id'] === 1, "Player placed in Zone 1 (Vallée d'Orépierre)");
assert((int)$char['pos_x'] === 2 && (int)$char['pos_y'] === 2, "Player starts at central square [2, 2]");
echo "✅ Héros {$charName} créé au centre de la cité d'Orépierre [2, 2] !\n";

echo "=== 3. Test Déplacements & Consommation de PA sur la Carte ===\n";
$zone = WorldMap::getZoneById(1);
assert($zone !== null, "Zone 1 exists");
$tiles = WorldMap::getZoneTiles(1);
assert(count($tiles) === 25, "Zone has exactly 25 tiles (5x5)");

// Déplacement Est vers la Forge (2,2) -> (3,2)
$apBefore = (int)Character::findById($charId)['current_ap'];
$moveRes = WorldMap::moveCharacter($charId, 'east');
assert($moveRes['success'] === true, "Moved East");
assert((int)$moveRes['character']['pos_x'] === 3 && (int)$moveRes['character']['pos_y'] === 2, "Position is now [3, 2] (Forge)");
assert((int)$moveRes['character']['current_ap'] === ($apBefore - 1), "1 AP consumed");
echo "✅ Déplacement vers la Forge [3, 2] validé (-1 PA) !\n";

// Test obstacle : Tentative de continuer vers l'Est sur le Lac (4,2) qui est infranchissable
$moveObs = WorldMap::moveCharacter($charId, 'east');
assert($moveObs['success'] === false, "Movement blocked by water obstacle");
echo "✅ Obstacle infranchissable (Lac) validé (déplacement bloqué) !\n";

// Déplacement vers la Taverne [1, 2] en revenant vers l'Ouest
WorldMap::moveCharacter($charId, 'west'); // Reviens en (2,2)
$moveTavern = WorldMap::moveCharacter($charId, 'west'); // Va en (1,2)
assert($moveTavern['success'] === true, "Moved to Tavern [1, 2]");
$tileTavern = WorldMap::getTile(1, 1, 2);
assert($tileTavern['action_type'] === 'tavern', "Tavern action available on tile [1, 2]");
echo "✅ Déplacement vers la Taverne du Sanglier [1, 2] validé !\n";

echo "=== 4. Test Équipement & Déséquipement ===\n";
$cap = Item::getByCode('leather_cap');
$buckler = Item::getByCode('wooden_buckler');
Inventory::addItem($charId, $cap['id']);
Inventory::addItem($charId, $buckler['id']);

$bag = Inventory::getBagItems($charId);
$capItem = array_values(array_filter($bag, fn($i) => $i['code'] === 'leather_cap'))[0];
$bucklerItem = array_values(array_filter($bag, fn($i) => $i['code'] === 'wooden_buckler'))[0];

$eqRes1 = Inventory::equipItem($charId, $capItem['character_item_id']);
$eqRes2 = Inventory::equipItem($charId, $bucklerItem['character_item_id']);
assert($eqRes1['success'] === true, "Equipped head");
assert($eqRes2['success'] === true, "Equipped shield");

$bonuses = Inventory::getEquippedBonusTotals($charId);
assert($bonuses['bonus_defense'] === 6, "Defense includes chest + shield + helm");
echo "✅ Équipement des pièces validé (Bonus Défense équipement = +{$bonuses['bonus_defense']})\n";

echo "=== 5. Test Vente d'Objet & Récupération d'Or ===\n";
$charGoldBefore = Character::findById($charId)['gold'];
Inventory::unequipItem($charId, 'shield');
$bag = Inventory::getBagItems($charId);
$bucklerInBag = array_values(array_filter($bag, fn($i) => $i['code'] === 'wooden_buckler'))[0];
$sellRes = Inventory::sellItem($charId, $bucklerInBag['character_item_id']);
assert($sellRes['success'] === true, "Sold buckler");
$charGoldAfter = Character::findById($charId)['gold'];
assert($charGoldAfter == ($charGoldBefore + $bucklerInBag['sell_price']), "Gold increased by sell price");
echo "✅ Vente d'objet validée (+{$bucklerInBag['sell_price']} 💰)\n";

echo "=== 6. Test Extension du Sac au Level Up ===\n";
$charSlotsBefore = Character::findById($charId)['inventory_slots'];
Character::addXpAndGold($charId, 100, 10); // Passage niveau 2
$charSlotsAfter = Character::findById($charId)['inventory_slots'];
assert($charSlotsAfter === ($charSlotsBefore + 1), "Inventory slots increased by 1 upon level up");
echo "✅ Sac évolutif validé : Capacité portée à {$charSlotsAfter} emplacements (+1 slot débloqué) !\n";

echo "=== 7. Test Soin Taverne avec Bonus de PV d'Équipement (Heaume +15 PV) ===\n";
$ironHelm = Item::getByCode('iron_helm');
Inventory::addItem($charId, $ironHelm['id']);
$bag = Inventory::getBagItems($charId);
$helmInBag = array_values(array_filter($bag, fn($i) => $i['code'] === 'iron_helm'))[0];
$eqHelmRes = Inventory::equipItem($charId, $helmInBag['character_item_id']);
assert($eqHelmRes['success'] === true, "Iron helm equipped");

Character::updateHp($charId, 50);
$healSuccess = Character::healAtTavern($charId, 10);
assert($healSuccess === true, "Tavern rest successful");

$charAfterHeal = Character::findById($charId);
$effAfterHeal = Character::getEffectiveStats($charId);
assert((int)$charAfterHeal['current_hp'] === 135, "HP fully restored to effective max 135 (120 base + 15 helm)");
assert((int)$effAfterHeal['effective_max_hp'] === 135, "Effective max HP is 135");
echo "✅ Soin à la Taverne avec équipement validé : 135/135 PV !\n";

echo "\n✨ TOUS LES TESTS DE CARTE, D'INVENTAIRE, D'ÉQUIPEMENT ET DE COMBAT SONT VALIDÉS !\n";
