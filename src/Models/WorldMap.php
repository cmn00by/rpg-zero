<?php
namespace Models;

use Core\Database;
use PDO;

class WorldMap {
    public static function getZoneById(int $zoneId): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM world_zones WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $zoneId]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getZoneTiles(int $zoneId): array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM map_tiles WHERE zone_id = :zid ORDER BY y ASC, x ASC");
        $stmt->execute(['zid' => $zoneId]);
        return $stmt->fetchAll();
    }

    public static function getTile(int $zoneId, int $x, int $y): ?array {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM map_tiles WHERE zone_id = :zid AND x = :x AND y = :y LIMIT 1");
        $stmt->execute(['zid' => $zoneId, 'x' => $x, 'y' => $y]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public static function getAdjacentTiles(int $zoneId, int $x, int $y): array {
        return [
            'north' => self::getTile($zoneId, $x, $y - 1),
            'south' => self::getTile($zoneId, $x, $y + 1),
            'west'  => self::getTile($zoneId, $x - 1, $y),
            'east'  => self::getTile($zoneId, $x + 1, $y),
        ];
    }

    public static function moveCharacter(int $characterId, string $direction): array {
        $char = Character::findById($characterId);
        if (!$char) {
            return ['success' => false, 'error' => 'Personnage introuvable.'];
        }

        $zoneId = (int)$char['current_zone_id'];
        $curX = (int)$char['pos_x'];
        $curY = (int)$char['pos_y'];

        $targetX = $curX;
        $targetY = $curY;

        switch (strtolower($direction)) {
            case 'north':
            case 'haut':
                $targetY--;
                break;
            case 'south':
            case 'bas':
                $targetY++;
                break;
            case 'west':
            case 'gauche':
            case 'ouest':
                $targetX--;
                break;
            case 'east':
            case 'droite':
            case 'est':
                $targetX++;
                break;
            default:
                return ['success' => false, 'error' => 'Direction invalide.'];
        }

        return self::executeMove($char, $zoneId, $targetX, $targetY);
    }

    public static function moveToCoord(int $characterId, int $targetX, int $targetY): array {
        $char = Character::findById($characterId);
        if (!$char) {
            return ['success' => false, 'error' => 'Personnage introuvable.'];
        }

        $zoneId = (int)$char['current_zone_id'];
        $curX = (int)$char['pos_x'];
        $curY = (int)$char['pos_y'];

        $dx = abs($targetX - $curX);
        $dy = abs($targetY - $curY);

        if (($dx + $dy) !== 1) {
            return ['success' => false, 'error' => 'Vous ne pouvez vous déplacer que sur une case adjacente (Nord, Sud, Est, Ouest).'];
        }

        return self::executeMove($char, $zoneId, $targetX, $targetY);
    }

    private static function executeMove(array $char, int $zoneId, int $targetX, int $targetY): array {
        $targetTile = self::getTile($zoneId, $targetX, $targetY);
        if (!$targetTile) {
            return ['success' => false, 'error' => 'Vous avez atteint les frontières infranchissables de la région.'];
        }

        if (!(int)$targetTile['is_walkable']) {
            return ['success' => false, 'error' => "Obstacle infranchissable : {$targetTile['name']} bloque le passage."];
        }

        $apCost = (int)$targetTile['ap_cost'];
        if ((int)$char['current_ap'] < $apCost) {
            return ['success' => false, 'error' => "Pas assez de Points d'Action ({$apCost} PA requis pour franchir ce terrain)."];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("
            UPDATE characters 
            SET current_ap = current_ap - :ap1, pos_x = :x, pos_y = :y, last_activity = NOW() 
            WHERE id = :id AND current_ap >= :ap2
        ");
        $stmt->execute([
            'ap1' => $apCost,
            'ap2' => $apCost,
            'x' => $targetX,
            'y' => $targetY,
            'id' => $char['id']
        ]);

        $updatedChar = Character::getEffectiveStats((int)$char['id']);

        return [
            'success' => true,
            'character' => $updatedChar,
            'tile' => $targetTile,
            'message' => "Vous vous déplacez vers {$targetTile['name']} (-{$apCost} PA)."
        ];
    }
}
