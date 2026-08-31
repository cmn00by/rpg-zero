<?php
namespace Controllers;

use Core\View;
use Core\Session;
use Models\Character;
use Models\Inventory;

class InventoryController {
    public function showInventory(): void {
        $charId = Session::getCharacterId();
        $character = Character::getEffectiveStats($charId);
        $bagItems = Inventory::getBagItems($charId);
        $equipped = Inventory::getEquippedItems($charId);
        $bonuses = Inventory::getEquippedBonusTotals($charId);

        View::render('inventory/index', [
            'title' => 'Sac & Équipements - RPG-Zero',
            'character' => $character,
            'bagItems' => $bagItems,
            'equipped' => $equipped,
            'bonuses' => $bonuses
        ]);
    }

    public function equip(): void {
        $charId = Session::getCharacterId();
        $charItemId = (int)($_POST['character_item_id'] ?? 0);
        $result = Inventory::equipItem($charId, $charItemId);
        $this->respondOrRedirect($charId, $result);
    }

    public function unequip(): void {
        $charId = Session::getCharacterId();
        $slot = trim($_POST['slot'] ?? '');
        $result = Inventory::unequipItem($charId, $slot);
        $this->respondOrRedirect($charId, $result);
    }

    public function useItem(): void {
        $charId = Session::getCharacterId();
        $charItemId = (int)($_POST['character_item_id'] ?? 0);
        $result = Inventory::consumeItem($charId, $charItemId);
        $this->respondOrRedirect($charId, $result);
    }

    public function sellItem(): void {
        $charId = Session::getCharacterId();
        $charItemId = (int)($_POST['character_item_id'] ?? 0);
        $result = Inventory::sellItem($charId, $charItemId);
        $this->respondOrRedirect($charId, $result);
    }

    private function respondOrRedirect(int $charId, array $result): void {
        if (isset($_SERVER['HTTP_HX_REQUEST'])) {
            $character = Character::getEffectiveStats($charId);
            $bagItems = Inventory::getBagItems($charId);
            $equipped = Inventory::getEquippedItems($charId);
            $bonuses = Inventory::getEquippedBonusTotals($charId);

            View::partial('inventory/partial_inventory', [
                'character' => $character,
                'bagItems' => $bagItems,
                'equipped' => $equipped,
                'bonuses' => $bonuses
            ]);
            exit;
        }

        if (!$result['success']) {
            Session::setFlash('error', $result['error'] ?? 'Action impossible.');
        } else {
            Session::setFlash('success', $result['message'] ?? 'Action effectuée.');
        }

        header('Location: /game/inventory');
        exit;
    }
}
