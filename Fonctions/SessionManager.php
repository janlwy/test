<?php

class SessionManager {
    private static $instance = null;
    private const SESSION_LIFETIME = 1800; // 30 minutes
    
    private function __construct() {
        // Constructeur privé pour le singleton
    }
    
    public static function getInstance(): SessionManager {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function startSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $this->setSecureCookieParams();
            session_start();
            $this->regenerateIfNeeded();
            $this->validateSession();
        }
    }
    
    private function setSecureCookieParams(): void {
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => '/',
            'domain' => '',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
    
    private function regenerateIfNeeded(): void {
        if (!isset($_SESSION['last_regeneration']) || 
            (time() - $_SESSION['last_regeneration']) > self::SESSION_LIFETIME) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    private function validateSession(): void {
        if (!isset($_COOKIE[session_name()])) {
            logError("Cookie de session manquant");
            $this->destroySession();
            header('Location: ?url=connexion/index');
            exit();
        }
    }
    
    public function destroySession(): void {
        $_SESSION = array();
        
        if (isset($_COOKIE[session_name()])) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
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
        return $token && $this->has('csrf_token') && hash_equals($this->get('csrf_token'), $token);
    }
    
    public function isAuthenticated(): bool {
        return $this->has('pseudo') && $this->has('user_id');
    }
    
    public function setAuthenticated(string $pseudo, int $userId): void {
        $this->set('pseudo', $pseudo);
        $this->set('user_id', $userId);
        $this->regenerateToken();
    }
}
