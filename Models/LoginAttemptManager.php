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
