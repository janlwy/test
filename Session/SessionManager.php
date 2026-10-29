<?php

namespace Session;

class SessionManager
{
    private static ?self $instance = null;
    private const SESSION_LIFETIME = 1800; // 30 minutes

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();

            if (!isset($_SESSION['initiated'])) {
                $_SESSION['initiated'] = true;
                $_SESSION['last_regeneration'] = time();
            } else {
                $this->regenerateIfNeeded();
            }

            $this->ensureCsrfToken();
        }
    }

    private function regenerateIfNeeded(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }
        if (
            !isset($_SESSION['last_regeneration']) ||
            (time() - $_SESSION['last_regeneration']) > self::SESSION_LIFETIME
        ) {
            if (!headers_sent() && session_regenerate_id(true)) {
                $_SESSION['last_regeneration'] = time();
            }
        }
    }

    public function destroySession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            if (isset($_COOKIE[session_name()]) && !headers_sent()) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }
            session_destroy();
        }
    }

    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function regenerateToken(): string
    {
        $token = bin2hex(random_bytes(32));
        $this->set('csrf_token', $token);
        $this->set('csrf_token_time', time());
        return $token;
    }

    public function validateToken(?string $token): bool
    {
        if (empty($token) || !$this->has('csrf_token')) {
            if (function_exists('logError')) {
                logError("Token CSRF manquant (Reçu: " . ($token ? 'oui' : 'non') . ", Session: " . ($this->has('csrf_token') ? 'oui' : 'non') . ")");
            }
            return false;
        }

        $tokenAge = time() - (int)$this->get('csrf_token_time', 0);
        if ($tokenAge > 3600) {
            $this->regenerateToken();
            return false;
        }

        return hash_equals((string)$this->get('csrf_token'), (string)$token);
    }

    public function ensureCsrfToken(): void
    {
        if (!$this->has('csrf_token') || empty($this->get('csrf_token'))) {
            $this->regenerateToken();
        }
    }

    public function validateFileUpload(array $file, array $allowedTypes, int $maxSize = 5242880): array
    {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['Fichier invalide ou upload incomplet.'];
        }

        $errors = [];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Erreur lors du téléchargement du fichier.';
            return $errors;
        }

        if ($file['size'] > $maxSize) {
            $errors[] = 'Le fichier est trop volumineux.';
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Type de fichier non autorisé.';
        }

        return $errors;
    }

    public function isAuthenticated(): bool
    {
        return $this->has('pseudo') && $this->has('user_id');
    }

    public function setAuthenticated(string $pseudo, int $userId, string $role = 'user'): void
    {
        $this->set('pseudo', $pseudo);
        $this->set('user_id', $userId);
        $this->set('role', $role);
        if ($role === 'admin') {
            $this->set('admin', true);
        }
        $this->regenerateToken();
    }
}
