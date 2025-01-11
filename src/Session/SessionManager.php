<?php

namespace App\Session;

class SessionManager
{
    private static $instance = null;
    private const SESSION_LIFETIME = 1800; // 30 minutes

    private function __construct()
    {
        // Constructeur privé pour le singleton
    }

    public static function getInstance(): SessionManager
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function isAuthenticated(): bool
    {
        // Implémentation de la vérification d'authentification
        return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function validateToken(string $token): bool
    {
        // Implémentation de la validation du token CSRF
        return $token === $_SESSION['csrf_token'];
    }
}
