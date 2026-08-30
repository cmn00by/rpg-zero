<?php
namespace Core;

class View {
    public static function render(string $template, array $data = [], ?string $layout = 'main'): void {
        extract($data);
        
        $viewPath = dirname(__DIR__) . "/Views/{$template}.php";
        if (!file_exists($viewPath)) {
            die("Vue introuvable : {$template}");
        }

        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        if ($layout === null) {
            echo $content;
            return;
        }

        $layoutPath = dirname(__DIR__) . "/Views/layouts/{$layout}.php";
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    public static function partial(string $template, array $data = []): void {
        self::render($template, $data, null);
    }

    public static function e(string $value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
