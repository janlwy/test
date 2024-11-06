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
        $timestamp = time();
        $this->set('csrf_token', $token);
        $this->set('csrf_token_time', $timestamp);
        return $token;
    }
    
    public function validateToken(?string $token): bool {
        if (!$token || !$this->has('csrf_token') || !$this->has('csrf_token_time')) {
            logError("Token CSRF manquant ou invalide");
            return false;
        }
        
        $tokenAge = time() - $this->get('csrf_token_time');
        if ($tokenAge > 3600) { // Token expire après 1 heure
            logError("Token CSRF expiré");
            return false;
        }
        
        $isValid = hash_equals($this->get('csrf_token'), $token);
        if (!$isValid) {
            logError("Token CSRF non valide");
        }
        return $isValid;
    }

    public function ensureCsrfToken(): void {
        if (!$this->has('csrf_token')) {
            $this->regenerateToken();
        }
    }
    
    public function validateFileUpload(array $file, array $allowedTypes, int $maxSize = 5242880): array {
        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return ['Fichier invalide ou upload incomplet.'];
        }
        
        // Vérification anti-virus si disponible
        if (function_exists('clamav_scan_file')) {
            $scan_result = clamav_scan_file($file['tmp_name']);
            if ($scan_result !== true) {
                return ['Le fichier pourrait contenir un virus.'];
            }
        }
        $errors = [];
        
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Erreur lors du téléchargement du fichier.';
            return $errors;
        }
        
        if ($file['size'] > $maxSize) {
            $errors[] = 'Le fichier est trop volumineux.';
        }
        
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        
        if (!in_array($mimeType, $allowedTypes)) {
            $errors[] = 'Type de fichier non autorisé.';
        }
        
        return $errors;
    }
    
    public function isAuthenticated(): bool {
        return $this->has('pseudo') && $this->has('user_id');
    }
    
    public function setAuthenticated(string $pseudo, int $userId, string $role = 'user'): void {
        $this->set('pseudo', $pseudo);
        $this->set('user_id', $userId);
        $this->set('role', $role);
        if ($role === 'admin') {
            $this->set('admin', true);
        }
        $this->regenerateToken();
    }
}
