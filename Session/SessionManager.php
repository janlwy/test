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
    
    public function ensureCsrfToken(): void {
        if (!$this->has('csrf_token')) {
            $this->regenerateToken();
        }
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
<?php
namespace Session;

class SessionManager {
    private static $instance = null;
    
    private const SESSION_LIFETIME = 1800; // 30 minutes
    
    private function __construct() {
        // Constructor is private for singleton pattern
    }
    
    public static function getInstance(): self {
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
        
        // Configuration des cookies de session
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'] ?? '',
            'secure' => ($_SERVER['HTTPS'] ?? 'off') === 'on',
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
        
        // Configuration PHP.ini pour la sécurité
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', ($_SERVER['HTTPS'] ?? 'off') === 'on' ? '1' : '0');
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_lifetime', '0'); // Expire à la fermeture du navigateur
        ini_set('session.gc_maxlifetime', '1440'); // 24 minutes
        ini_set('session.cookie_secure', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.hash_function', 'sha256');
        ini_set('session.hash_bits_per_character', '5');
        
        // Protection contre les attaques de fixation de session
        if (empty($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
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
            if (function_exists('logError')) {
                logError("Cookie de session manquant");
            }
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
        try {
            $token = bin2hex(random_bytes(32));
            $timestamp = time();
            $this->set('csrf_token', $token);
            $this->set('csrf_token_time', $timestamp);
            $this->set('csrf_token_ip', $_SERVER['REMOTE_ADDR'] ?? '');
            $this->set('csrf_token_user_agent', $_SERVER['HTTP_USER_AGENT'] ?? '');
            
            // Ajout d'un sel unique pour renforcer la sécurité
            $salt = bin2hex(random_bytes(16));
            $this->set('csrf_token_salt', $salt);
            
            // Hashage du token avec le sel
            $hashedToken = hash('sha256', $token . $salt);
            $this->set('csrf_token_hashed', $hashedToken);
            
            return $token;
        } catch (\Exception $e) {
            if (function_exists('logError')) {
                logError('Erreur lors de la génération du token CSRF : ' . $e->getMessage());
            }
            throw new \RuntimeException('Impossible de générer un token de sécurité');
        }
    }
    
    public function validateToken(?string $token): bool {
        if (!$token || !$this->has('csrf_token') || !$this->has('csrf_token_time')) {
            if (function_exists('logError')) {
                logError("Token CSRF manquant ou invalide");
            }
            return false;
        }
        
        $tokenAge = time() - $this->get('csrf_token_time');
        if ($tokenAge > 3600) { // Token expire après 1 heure
            if (function_exists('logError')) {
                logError("Token CSRF expiré");
            }
            return false;
        }
        
        $isValid = hash_equals($this->get('csrf_token'), $token);
        if (!$isValid && function_exists('logError')) {
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
        
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
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
