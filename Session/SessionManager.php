<?php
namespace Session;

class SessionManager {
    private static $instance = null;
    
    private const SESSION_LIFETIME = 1800; // 30 minutes
    
    private function __construct() {
        // Constructor is private for singleton pattern
    }
    
    public function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => self::SESSION_LIFETIME,
                'path' => '/',
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            session_start();
        }
    }
    
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    
    public function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    public function regenerateToken(): string {
        $token = bin2hex(random_bytes(32));
        $this->set('csrf_token', $token);
        return $token;
    }
    
    public function validateToken(?string $token): bool {
        if (!$token || !$this->has('csrf_token')) {
            return false;
        }
        return hash_equals($this->get('csrf_token'), $token);
    }
    
    public function isAuthenticated(): bool {
        return $this->has('user_id') && $this->has('pseudo');
    }
    
    public function validateFileUpload(array $file, array $allowedTypes, int $maxSize): array {
        $errors = [];
        
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = "Aucun fichier uploadé";
            return $errors;
        }
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Erreur lors de l'upload: " . $file['error'];
            return $errors;
        }
        
        if ($file['size'] > $maxSize) {
            $errors[] = "Le fichier est trop volumineux (max " . ($maxSize / 1024 / 1024) . "MB)";
        }
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = "Type de fichier non autorisé ($mimeType)";
        }
        
        return $errors;
    }
}
