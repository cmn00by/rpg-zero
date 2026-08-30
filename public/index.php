<?php
declare(strict_types=1);

// Configuration et gestion des erreurs
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Autoloader PSR-4 simple pour src/
spl_autoload_register(function ($class) {
    $prefix = '';
    $base_dir = dirname(__DIR__) . '/src/';
    
    $file = $base_dir . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use Core\Router;
use Core\Session;
use Controllers\AuthController;
use Controllers\CharacterController;
use Controllers\GameController;
use Controllers\BattleController;

Session::start();

$router = new Router();

// Routes publiques (Authentification)
$router->get('/', [AuthController::class, 'showLogin']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'processRegister']);
$router->get('/logout', [AuthController::class, 'logout']);

// Routes nécessitant d'être connecté
$router->get('/character/create', [CharacterController::class, 'showCreate'], true, false);
$router->post('/character/create', [CharacterController::class, 'processCreate'], true, false);

// Routes nécessitant d'être connecté ET d'avoir un personnage
$router->get('/game/hub', [GameController::class, 'showHub'], true, true);
$router->post('/game/tavern/rest', [GameController::class, 'restAtTavern'], true, true);
$router->get('/game/stats', [CharacterController::class, 'showStats'], true, true);
$router->post('/character/allocate-stat', [CharacterController::class, 'allocateStat'], true, true);

// Combats & Exploration
$router->get('/battle/explore', [BattleController::class, 'showExplore'], true, true);
$router->post('/battle/start', [BattleController::class, 'startBattle'], true, true);
$router->get('/battle/arena', [BattleController::class, 'showArena'], true, true);
$router->post('/battle/action', [BattleController::class, 'action'], true, true);

// Exécution de la requête
$router->resolve();
