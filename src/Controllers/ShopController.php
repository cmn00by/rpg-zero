<?php
namespace Controllers;

use Core\View;
use Core\Session;
use Core\Database;
use Models\Character;
use Models\Item;
use Models\Inventory;
use Models\WorldMap;

class ShopController {
    public function showShop(): void {
        $charId = Session::getCharacterId();
        $character = Character::getEffectiveStats($charId);
        $zoneId = (int)($character['current_zone_id'] ?? 1);
        $curX = (int)($character['pos_x'] ?? 2);
        $curY = (int)($character['pos_y'] ?? 2);

        $currentTile = WorldMap::getTile($zoneId, $curX, $curY);
        $items = Item::getAll();

        View::render('shop/index', [
            'title' => "L'Échoppe du Forgeron - RPG-Zero",
            'character' => $character,
            'currentTile' => $currentTile,
            'items' => $items
        ]);
    }

    public function buy(): void {
        $charId = Session::getCharacterId();
        $itemId = (int)($_POST['item_id'] ?? 0);

        $item = Item::getById($itemId);
        if (!$item) {
            Session::setFlash('error', 'Objet introuvable.');
            header('Location: /game/shop');
            exit;
        }

        $char = Character::findById($charId);
        $price = (int)$item['buy_price'];

        if ((int)$char['gold'] < $price) {
            Session::setFlash('error', "Vous n'avez pas assez de pièces d'or (Requis : {$price} 💰, Vous avez : {$char['gold']} 💰).");
            header('Location: /game/shop');
            exit;
        }

        $addRes = Inventory::addItem($charId, $itemId, 1);
        if (!$addRes['success']) {
            Session::setFlash('error', $addRes['error']);
            header('Location: /game/shop');
            exit;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE characters SET gold = gold - :price, last_activity = NOW() WHERE id = :id");
        $stmt->execute(['price' => $price, 'id' => $charId]);

        Session::setFlash('success', "Vous avez acheté : {$item['name']} pour {$price} pièces d'or 💰 !");
        header('Location: /game/shop');
        exit;
    }
}
