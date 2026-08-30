<?php
namespace Controllers;

use Core\View;
use Core\Session;
use Models\User;
use Models\Character;

class AuthController {
    public function showLogin(): void {
        if (Session::getUserId()) {
            header('Location: /game/hub');
            exit;
        }
        View::render('auth/login', ['title' => 'Connexion - RPG-Zero']);
    }

    public function processLogin(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            Session::setFlash('error', 'Veuillez remplir tous les champs.');
            header('Location: /login');
            exit;
        }

        $user = User::findByUsername($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Session::setFlash('error', 'Identifiants invalides.');
            header('Location: /login');
            exit;
        }

        Session::setUserId((int)$user['id']);

        $char = Character::findByUserId((int)$user['id']);
        if ($char) {
            Session::setCharacterId((int)$char['id']);
            Session::setFlash('success', "Bienvenue, {$char['name']} !");
            header('Location: /game/hub');
        } else {
            Session::setCharacterId(null);
            Session::setFlash('info', 'Créez votre premier aventurier pour débuter l\'aventure.');
            header('Location: /character/create');
        }
        exit;
    }

    public function showRegister(): void {
        if (Session::getUserId()) {
            header('Location: /game/hub');
            exit;
        }
        View::render('auth/register', ['title' => 'Inscription - RPG-Zero']);
    }

    public function processRegister(): void {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['password_confirm'] ?? '';

        if (empty($username) || empty($password) || empty($confirm)) {
            Session::setFlash('error', 'Veuillez remplir tous les champs.');
            header('Location: /register');
            exit;
        }

        if (strlen($username) < 3 || strlen($username) > 20) {
            Session::setFlash('error', 'Le nom d\'utilisateur doit contenir entre 3 et 20 caractères.');
            header('Location: /register');
            exit;
        }

        if ($password !== $confirm) {
            Session::setFlash('error', 'Les mots de passe ne correspondent pas.');
            header('Location: /register');
            exit;
        }

        if (strlen($password) < 6) {
            Session::setFlash('error', 'Le mot de passe doit comporter au moins 6 caractères.');
            header('Location: /register');
            exit;
        }

        if (User::findByUsername($username)) {
            Session::setFlash('error', 'Ce nom d\'utilisateur est déjà utilisé.');
            header('Location: /register');
            exit;
        }

        $userId = User::create($username, $password);
        Session::setUserId($userId);
        Session::setCharacterId(null);

        Session::setFlash('success', 'Votre compte a été créé avec succès ! Créez maintenant votre héros.');
        header('Location: /character/create');
        exit;
    }

    public function logout(): void {
        Session::destroy();
        header('Location: /login');
        exit;
    }
}
