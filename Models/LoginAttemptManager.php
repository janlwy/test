<?php

class LoginAttemptManager {
    private const MAX_ATTEMPTS = 3;
    private const LOCKOUT_TIME = 900; // 15 minutes in seconds
    private const ATTEMPT_WINDOW = 3600; // 1 hour in seconds
    
    private $manager;
    
    public function __construct() {
        $this->manager = new Manager();
    }
    
    public function recordAttempt(string $identifier, string $ip): void {
        try {
            $connection = $this->manager->getConnexion();
            $sql = "INSERT INTO login_attempts (identifier, ip_address, attempt_time) 
                   VALUES (:identifier, :ip, :time)";
            
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':identifier' => $identifier,
                ':ip' => $ip,
                ':time' => date('Y-m-d H:i:s')
            ]);
            
            logInfo("Tentative de connexion échouée enregistrée pour $identifier depuis $ip");
        } catch (Exception $e) {
            logError("Erreur lors de l'enregistrement de la tentative de connexion: " . $e->getMessage());
        }
    }
    
    public function isLockedOut(string $identifier, string $ip): bool {
        try {
            $connection = $this->manager->getConnexion();
            $sql = "SELECT COUNT(*) FROM login_attempts 
                   WHERE (identifier = :identifier OR ip_address = :ip)
                   AND attempt_time > DATE_SUB(NOW(), INTERVAL :lockout_time SECOND)";
            
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':identifier' => $identifier,
                ':ip' => $ip,
                ':lockout_time' => self::LOCKOUT_TIME
            ]);
            
            $attempts = (int)$stmt->fetchColumn();
            $isLocked = $attempts >= self::MAX_ATTEMPTS;
            
            if ($isLocked) {
                logInfo("Compte verrouillé pour $identifier depuis $ip");
            }
            
            return $isLocked;
        } catch (Exception $e) {
            logError("Erreur lors de la vérification du verrouillage: " . $e->getMessage());
            return false;
        }
    }
    
    public function getRemainingAttempts(string $identifier, string $ip): int {
        try {
            $connection = $this->manager->getConnexion();
            $sql = "SELECT COUNT(*) FROM login_attempts 
                   WHERE (identifier = :identifier OR ip_address = :ip)
                   AND attempt_time > DATE_SUB(NOW(), INTERVAL :attempt_window SECOND)";
            
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                ':identifier' => $identifier,
                ':ip' => $ip,
                ':attempt_window' => self::ATTEMPT_WINDOW
            ]);
            
            $attempts = (int)$stmt->fetchColumn();
            return max(0, self::MAX_ATTEMPTS - $attempts);
        } catch (Exception $e) {
            logError("Erreur lors du calcul des tentatives restantes: " . $e->getMessage());
            return 0;
        }
    }
    
    public function cleanupOldAttempts(): void {
        try {
            $connection = $this->manager->getConnexion();
            $sql = "DELETE FROM login_attempts 
                   WHERE attempt_time < DATE_SUB(NOW(), INTERVAL :attempt_window SECOND)";
            
            $stmt = $connection->prepare($sql);
            $stmt->execute([':attempt_window' => self::ATTEMPT_WINDOW]);
            
            logInfo("Nettoyage des anciennes tentatives de connexion effectué");
        } catch (Exception $e) {
            logError("Erreur lors du nettoyage des anciennes tentatives: " . $e->getMessage());
        }
    }
}
<?php
namespace Models;

use PDO;

class LoginAttemptManager {
    private const MAX_ATTEMPTS = 3;
    private const LOCKOUT_TIME = 900; // 15 minutes in seconds
    private $connexion;

    public function __construct() {
        $this->connexion = new Manager();
    }

    public function recordAttempt(string $pseudo, string $ip): void {
        $sql = "INSERT INTO login_attempts (pseudo, ip_address, attempt_time) VALUES (:pseudo, :ip, NOW())";
        $stmt = $this->connexion->getConnexion()->prepare($sql);
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':ip' => $ip
        ]);
    }

    public function isLockedOut(string $pseudo, string $ip): bool {
        $sql = "SELECT COUNT(*) FROM login_attempts 
                WHERE (pseudo = :pseudo OR ip_address = :ip)
                AND attempt_time > DATE_SUB(NOW(), INTERVAL " . self::LOCKOUT_TIME . " SECOND)";
        
        $stmt = $this->connexion->getConnexion()->prepare($sql);
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':ip' => $ip
        ]);
        
        return $stmt->fetchColumn() >= self::MAX_ATTEMPTS;
    }

    public function getRemainingAttempts(string $pseudo, string $ip): int {
        $sql = "SELECT COUNT(*) FROM login_attempts 
                WHERE (pseudo = :pseudo OR ip_address = :ip)
                AND attempt_time > DATE_SUB(NOW(), INTERVAL " . self::LOCKOUT_TIME . " SECOND)";
        
        $stmt = $this->connexion->getConnexion()->prepare($sql);
        $stmt->execute([
            ':pseudo' => $pseudo,
            ':ip' => $ip
        ]);
        
        $attempts = $stmt->fetchColumn();
        return max(0, self::MAX_ATTEMPTS - $attempts);
    }

    public function cleanupOldAttempts(): void {
        $sql = "DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL " . self::LOCKOUT_TIME . " SECOND)";
        $this->connexion->getConnexion()->exec($sql);
    }
}
