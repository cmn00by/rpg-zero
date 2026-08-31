<?php
// Test E2E de logique de jeu RPG-Zero avec Inventaire & Équipements
require_once __DIR__ . '/../src/Core/Database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Models/Character.php';
require_once __DIR__ . '/../src/Models/Monster.php';
require_once __DIR__ . '/../src/Models/Battle.php';
require_once __DIR__ . '/../src/Models/Level.php';
require_once __DIR__ . '/../src/Models/Item.php';
require_once __DIR__ . '/../src/Models/Inventory.php';

use Models\User;
use Models\Character;
use Models\Monster;
use Models\Battle;
use Models\Level;
use Models\Item;
use Models\Inventory;

echo "=== 1. Test Catalogue d'Objets ===\n";
$items = Item::getAll();
assert(count($items) >= 15, "At least 15 items in catalog");
$ironSword = Item::getByCode('iron_sword');
assert($ironSword !== null, "Iron sword exists");
assert((int)$ironSword['bonus_attack'] === 7, "Iron sword attack is +7");
echo "✅ Catalogue d'objets validé (" . count($items) . " objets chargés)\n";

echo "=== 2. Test Création de Personnage avec Équipement de Départ ===\n";
$testUsername = 'hero_' . time();
$userId = User::create($testUsername, 'password123');
$charName = 'Perceval_' . rand(100, 999);
$charId = Character::create($userId, 1, $charName); // Guerrier

$equipped = Inventory::getEquippedItems($charId);
assert($equipped['weapon'] !== null, "Starting weapon equipped");
assert($equipped['chest'] !== null, "Starting armor equipped");

$bag = Inventory::getBagItems($charId);
assert(count($bag) === 1, "1 item type in bag (potions stack)");
assert($bag[0]['code'] === 'health_potion_minor', "Potions in bag");
assert((int)$bag[0]['quantity'] === 2, "2 potions in bag");

$effStats = Character::getEffectiveStats($charId);
assert($effStats['total_attack'] >= 21, "Total attack includes weapon + stats");
echo "✅ Héros {$charName} créé avec équipement de départ (Épée en fer, Tunique, 2 Potions de soin) !\n";

echo "=== 3. Test Consommation de Potion ===\n";
Character::updateHp($charId, 30);
$charBefore = Character::findById($charId);
assert($charBefore['current_hp'] == 30, "HP lowered to 30");

$consumeRes = Inventory::consumeItem($charId, $bag[0]['character_item_id']);
assert($consumeRes['success'] === true, "Potion consumed");
$charAfter = Character::findById($charId);
assert($charAfter['current_hp'] == 65, "HP restored to 65 (+35)");

$bagAfter1 = Inventory::getBagItems($charId);
assert((int)$bagAfter1[0]['quantity'] === 1, "1 potion remaining in stack");
echo "✅ Consommation de potion validée (+35 PV, 1 potion restante)\n";

echo "=== 4. Test Équipement & Déséquipement ===\n";
// Donner un casque en cuir et un bouclier au joueur
$cap = Item::getByCode('leather_cap');
$buckler = Item::getByCode('wooden_buckler');
Inventory::addItem($charId, $cap['id']);
Inventory::addItem($charId, $buckler['id']);

$bag = Inventory::getBagItems($charId);
$capItem = array_values(array_filter($bag, fn($i) => $i['code'] === 'leather_cap'))[0];
$bucklerItem = array_values(array_filter($bag, fn($i) => $i['code'] === 'wooden_buckler'))[0];

// Équiper casque et bouclier
$eqRes1 = Inventory::equipItem($charId, $capItem['character_item_id']);
$eqRes2 = Inventory::equipItem($charId, $bucklerItem['character_item_id']);
assert($eqRes1['success'] === true, "Equipped head");
assert($eqRes2['success'] === true, "Equipped shield");

$equippedAfter = Inventory::getEquippedItems($charId);
assert($equippedAfter['head'] !== null, "Head slot occupied");
assert($equippedAfter['shield'] !== null, "Shield slot occupied");

$bonuses = Inventory::getEquippedBonusTotals($charId);
// 1 (robe) + 2 (casque) + 3 (bouclier) = 6
assert($bonuses['bonus_defense'] === 6, "Defense includes chest + shield + helm");
echo "✅ Équipement des pièces validé (Bonus Défense équipement = +{$bonuses['bonus_defense']})\n";

// Déséquiper le bouclier
$uneqRes = Inventory::unequipItem($charId, 'shield');
assert($uneqRes['success'] === true, "Unequipped shield");
$equippedAfter2 = Inventory::getEquippedItems($charId);
assert($equippedAfter2['shield'] === null, "Shield slot empty");
echo "✅ Déséquipement validé (Bouclier retourné dans le sac)\n";

echo "=== 5. Test Vente d'Objet ===\n";
$charGoldBefore = Character::findById($charId)['gold'];
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

echo "\n✨ TOUS LES TESTS D'INVENTAIRE ET D'ÉQUIPEMENT SONT VALIDÉS AVEC SUCCÈS !\n";
