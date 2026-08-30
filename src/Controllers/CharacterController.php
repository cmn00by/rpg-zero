<?php
namespace Controllers;

use Core\View;
use Core\Session;
use Models\Character;

class CharacterController {
    public function showCreate(): void {
        $userId = Session::getUserId();
        $existing = Character::findByUserId($userId);
        if ($existing) {
            Session::setCharacterId((int)$existing['id']);
            header('Location: /game/hub');
            exit;
        }

        $classes = Character::getClasses();
        View::render('character/create', [
            'title' => 'Créer votre Héros - RPG-Zero',
            'classes' => $classes
        ]);
    }

    public function processCreate(): void {
        $userId = Session::getUserId();
        $name = trim($_POST['name'] ?? '');
        $classId = (int)($_POST['class_id'] ?? 0);

        if (empty($name) || $classId <= 0) {
            Session::setFlash('error', 'Veuillez choisir un nom et une classe.');
            header('Location: /character/create');
            exit;
        }

        if (strlen($name) < 3 || strlen($name) > 20) {
            Session::setFlash('error', 'Le nom doit contenir entre 3 et 20 caractères.');
            header('Location: /character/create');
            exit;
        }

        try {
            $charId = Character::create($userId, $classId, $name);
            Session::setCharacterId($charId);
            Session::setFlash('success', "Gloire au héros {$name} ! Votre légende commence dès maintenant.");
            header('Location: /game/hub');
            exit;
        } catch (\Exception $e) {
            Session::setFlash('error', 'Erreur lors de la création : ce nom est peut-être déjà pris.');
            header('Location: /character/create');
            exit;
        }
    }

    public function showStats(): void {
        $charId = Session::getCharacterId();
        $character = Character::findById($charId);
        View::render('game/stats', [
            'title' => 'Feuille de personnage',
            'character' => $character
        ]);
    }
}
