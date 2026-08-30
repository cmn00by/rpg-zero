<?php
namespace Core;

class Router {
    private array $routes = [];

    public function get(string $path, array $handler, bool $auth = false, bool $requiresCharacter = false): void {
        $this->addRoute('GET', $path, $handler, $auth, $requiresCharacter);
    }

    public function post(string $path, array $handler, bool $auth = false, bool $requiresCharacter = false): void {
        $this->addRoute('POST', $path, $handler, $auth, $requiresCharacter);
    }

    private function addRoute(string $method, string $path, array $handler, bool $auth, bool $requiresCharacter): void {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler,
            'auth' => $auth,
            'requiresCharacter' => $requiresCharacter
        ];
    }

    public function resolve(): void {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $uri) {
                // Vérification d'authentification
                if ($route['auth'] && !Session::getUserId()) {
                    Session::setFlash('error', 'Vous devez être connecté pour accéder à cette page.');
                    header('Location: /login');
                    exit;
                }

                // Vérification de personnage
                if ($route['requiresCharacter'] && !Session::getCharacterId()) {
                    Session::setFlash('warning', 'Veuillez d\'abord créer ou sélectionner un personnage.');
                    header('Location: /character/create');
                    exit;
                }

                [$controllerClass, $action] = $route['handler'];
                $controller = new $controllerClass();
                $controller->$action();
                return;
            }
        }

        http_response_code(404);
        View::render('errors/404', ['title' => 'Page non trouvée']);
    }
}
