<?php
namespace Controllers;

use Core\View;
use Core\Session;
use Models\Character;
use Models\WorldMap;

class MapController {
    public function showMap(): void {
        $charId = Session::getCharacterId();
        $character = Character::getEffectiveStats($charId);
        $zoneId = (int)($character['current_zone_id'] ?? 1);
        $curX = (int)($character['pos_x'] ?? 2);
        $curY = (int)($character['pos_y'] ?? 2);

        $zone = WorldMap::getZoneById($zoneId);
        $tiles = WorldMap::getZoneTiles($zoneId);
        $currentTile = WorldMap::getTile($zoneId, $curX, $curY);
        $adjacent = WorldMap::getAdjacentTiles($zoneId, $curX, $curY);

        View::render('map/index', [
            'title' => ($zone['name'] ?? 'Carte du Monde') . ' - RPG-Zero',
            'character' => $character,
            'zone' => $zone,
            'tiles' => $tiles,
            'currentTile' => $currentTile,
            'adjacent' => $adjacent
        ]);
    }

    public function move(): void {
        $charId = Session::getCharacterId();
        $dir = trim($_POST['direction'] ?? '');

        $result = WorldMap::moveCharacter($charId, $dir);

        if (!$result['success']) {
            Session::setFlash('error', $result['error']);
        }

        $this->respondOrRedirect($charId);
    }

    public function moveToCoord(): void {
        $charId = Session::getCharacterId();
        $x = (int)($_POST['x'] ?? 0);
        $y = (int)($_POST['y'] ?? 0);

        $result = WorldMap::moveToCoord($charId, $x, $y);

        if (!$result['success']) {
            Session::setFlash('error', $result['error']);
        }

        $this->respondOrRedirect($charId);
    }

    private function respondOrRedirect(int $charId): void {
        if (isset($_SERVER['HTTP_HX_REQUEST'])) {
            $character = Character::getEffectiveStats($charId);
            $zoneId = (int)($character['current_zone_id'] ?? 1);
            $curX = (int)($character['pos_x'] ?? 2);
            $curY = (int)($character['pos_y'] ?? 2);

            $zone = WorldMap::getZoneById($zoneId);
            $tiles = WorldMap::getZoneTiles($zoneId);
            $currentTile = WorldMap::getTile($zoneId, $curX, $curY);
            $adjacent = WorldMap::getAdjacentTiles($zoneId, $curX, $curY);

            View::partial('map/partial_map', [
                'character' => $character,
                'zone' => $zone,
                'tiles' => $tiles,
                'currentTile' => $currentTile,
                'adjacent' => $adjacent
            ]);
            exit;
        }

        header('Location: /game/map');
        exit;
    }
}
