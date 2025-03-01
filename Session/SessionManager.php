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
        // Vérifier si une session est déjà active
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }
        
        // Vérifier si une session peut être démarrée
        if (session_status() === PHP_SESSION_NONE) {
            try {
                // Configurer les paramètres de sécurité des cookies
                $this->setSecureCookieParams();
                
                // Démarrer la session
                session_start();
                
                // S'assurer que la session est bien démarrée avant de continuer
                if (session_status() === PHP_SESSION_ACTIVE) {
                    // Initialiser la session si nécessaire
                    if (!isset($_SESSION['initiated'])) {
                        $_SESSION['initiated'] = true;
                        $_SESSION['last_regeneration'] = time();
                    } else {
                        // Régénérer l'ID de session si nécessaire
                        $this->regenerateIfNeeded();
                    }
                    
                    // Valider la session
                    $this->validateSession();
                } else {
                    // Journaliser l'erreur si la session n'a pas pu être démarrée
                    if (function_exists('logError')) {
                        logError("Impossible de démarrer la session");
                    }
                }
            } catch (\Exception $e) {
                if (function_exists('logError')) {
                    logError("Erreur lors du démarrage de la session: " . $e->getMessage());
                }
            }
        }
    }
    
    private function setSecureCookieParams(): void {
        $cookieParams = session_get_cookie_params();
        
        // Déterminer si la connexion est sécurisée
        $isSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        
        // Configuration des cookies de session
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'] ?? '',
            'secure' => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax' // Changé de Strict à Lax pour plus de compatibilité
        ]);
        
        // Configuration PHP.ini pour la sécurité
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_secure', $isSecure ? '1' : '0');
        ini_set('session.cookie_samesite', 'Lax');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.cookie_lifetime', '0'); // Expire à la fermeture du navigateur
        ini_set('session.gc_maxlifetime', '1440'); // 24 minutes
        ini_set('session.use_trans_sid', '0');
        ini_set('session.hash_function', 'sha256');
        ini_set('session.hash_bits_per_character', '5');
        
        // Ne pas régénérer l'ID de session ici, car $_SESSION n'est pas encore disponible
    }
    
    private function regenerateIfNeeded(): void {
        // Double vérification que la session est active et initialisée
        if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION)) {
            return;
        }
        
        try {
            if (!isset($_SESSION['last_regeneration']) || 
                (time() - $_SESSION['last_regeneration']) > self::SESSION_LIFETIME) {
                // Vérifier que la session est active avant de régénérer l'ID
                if (session_status() === PHP_SESSION_ACTIVE) {
                    if (session_regenerate_id(true)) {
                        $_SESSION['last_regeneration'] = time();
                    }
                }
            }
        } catch (\Exception $e) {
            // Capturer toute exception qui pourrait survenir
            if (function_exists('logError')) {
                logError("Erreur lors de la régénération de l'ID de session: " . $e->getMessage());
            }
        }
    }
    
    private function validateSession(): void {
        // Vérifier si une session est active
        if (session_status() !== PHP_SESSION_ACTIVE || !isset($_SESSION)) {
            return;
        }
        
        // Vérifier si le cookie de session existe
        if (!isset($_COOKIE[session_name()])) {
            if (function_exists('logError')) {
                logError("Cookie de session manquant");
            }
            
            // Ne pas rediriger automatiquement, juste détruire la session
            $this->destroySession();
            
            // Éviter de rediriger si nous sommes déjà sur la page de connexion
            if (!isset($_GET['url']) || $_GET['url'] !== 'connexion/index') {
                header('Location: ?url=connexion/index');
                exit();
            }
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
