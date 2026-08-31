<?php
namespace Controllers;

use Core\View;
use Core\Session;
use Models\Character;
use Models\Monster;
use Models\Battle;

class BattleController {
    public function showExplore(): void {
        $charId = Session::getCharacterId();
        $character = Character::getEffectiveStats($charId);
        $activeBattle = Battle::getActiveBattle($charId);

        if ($activeBattle) {
            header('Location: /battle/arena');
            exit;
        }

        View::render('battle/explore', [
            'title' => 'Exploration & Aventures',
            'character' => $character
        ]);
    }

    public function startBattle(): void {
        $charId = Session::getCharacterId();
        $character = Character::getEffectiveStats($charId);
        $apCost = 5;

        if ($character['current_hp'] <= 0) {
            Session::setFlash('error', 'Vous êtes inconscient ! Reposez-vous d\'abord à la taverne.');
            header('Location: /game/hub');
            exit;
        }

        if ($character['current_ap'] < $apCost) {
            Session::setFlash('error', "Points d'Action (PA) insuffisants ({$apCost} PA requis).");
            header('Location: /battle/explore');
            exit;
        }

        $monster = Monster::getRandomByZone('forest');
        if (!$monster) {
            Session::setFlash('error', 'Aucun monstre trouvé dans cette zone.');
            header('Location: /battle/explore');
            exit;
        }

        Character::consumeAp($charId, $apCost);
        Battle::create($charId, (int)$monster['id']);

        header('Location: /battle/arena');
        exit;
    }

    public function showArena(): void {
        $charId = Session::getCharacterId();
        $character = Character::getEffectiveStats($charId);
        $battle = Battle::getActiveBattle($charId);

        if (!$battle) {
            header('Location: /battle/explore');
            exit;
        }

        $logs = Battle::getLogs((int)$battle['id']);
        $summary = Battle::calculateDuelSummary((int)$battle['id'], $battle, $character, $logs, $battle['winner'], null);

        View::render('battle/arena', [
            'title' => "⚔️ Duel contre {$battle['monster_name']} - RPG-Zero",
            'character' => $character,
            'battle' => $battle,
            'logs' => $logs,
            'summary' => $summary
        ]);
    }

    public function action(): void {
        $charId = Session::getCharacterId();
        $battle = Battle::getActiveBattle($charId);

        if (!$battle) {
            if (isset($_SERVER['HTTP_HX_REQUEST'])) {
                header('HX-Redirect: /battle/explore');
                exit;
            }
            header('Location: /battle/explore');
            exit;
        }

        $action = $_POST['action'] ?? 'attack';
        $result = Battle::processTurn((int)$battle['id'], $action);

        if (isset($_SERVER['HTTP_HX_REQUEST'])) {
            View::partial('battle/partial_combat_log', [
                'battle' => $result['battle'] ?? Battle::getById((int)$battle['id']),
                'character' => $result['character'] ?? Character::getEffectiveStats($charId),
                'logs' => $result['logs'] ?? Battle::getLogs((int)$battle['id']),
                'is_finished' => $result['is_finished'] ?? false,
                'winner' => $result['winner'] ?? null,
                'rewards' => $result['rewards'] ?? null,
                'summary' => $result['summary'] ?? null
            ]);
            exit;
        }

        header('Location: /battle/arena');
        exit;
    }
}
