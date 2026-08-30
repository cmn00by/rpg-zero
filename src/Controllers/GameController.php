<?php
namespace Controllers;

use Core\View;
use Core\Session;
use Models\Character;
use Models\Battle;

class GameController {
    public function showHub(): void {
        $charId = Session::getCharacterId();
        $character = Character::findById($charId);

        // Si le personnage est actuellement engagé dans un combat actif non terminé, le rediriger vers l'arène
        $activeBattle = Battle::getActiveBattle($charId);

        View::render('game/hub', [
            'title' => 'La Cité d\'Orépierre - Hub',
            'character' => $character,
            'activeBattle' => $activeBattle
        ]);
    }

    public function restAtTavern(): void {
        $charId = Session::getCharacterId();
        $cost = 10;
        $character = Character::findById($charId);

        if ($character['gold'] < $cost) {
            Session::setFlash('error', "Vous n'avez pas assez d'or ({$cost} pièces requises).");
            header('Location: /game/hub');
            exit;
        }

        Character::healAtTavern($charId, $cost);
        Session::setFlash('success', "🍺 Une bonne chope d'hydromel et une nuit au chaud restaurent tous vos PV et PA ! (-{$cost} or)");
        header('Location: /game/hub');
        exit;
    }
}
