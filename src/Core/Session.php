<?php
namespace Core;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        self::start();
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        self::start();
        unset($_SESSION[$key]);
    }

    public static function setFlash(string $type, string $message): void {
        self::start();
        if (!isset($_SESSION['flash'][$type]) || !in_array($message, $_SESSION['flash'][$type], true)) {
            $_SESSION['flash'][$type][] = $message;
        }
    }

    public static function getFlashes(): array {
        self::start();
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $flashes;
    }

    public static function getUserId(): ?int {
        return self::get('user_id');
    }

    public static function getCharacterId(): ?int {
        return self::get('character_id');
    }

    public static function setUserId(int $id): void {
        self::set('user_id', $id);
    }

    public static function setCharacterId(?int $id): void {
        if ($id === null) {
            self::remove('character_id');
        } else {
            self::set('character_id', $id);
        }
    }

    public static function destroy(): void {
        self::start();
        session_destroy();
    }
}
