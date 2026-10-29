<?php

namespace Models;

use PDO;
use PDOException;

class LoginAttemptManager
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 900; // 15 minutes
    private Manager $manager;

    public function __construct()
    {
        $this->manager = new Manager();
        $this->ensureTableExists();
    }

    private function ensureTableExists(): void
    {
        try {
            $sql = "CREATE TABLE IF NOT EXISTS `login_attempts` (
                `id` INT(11) NOT NULL AUTO_INCREMENT,
                `pseudo` VARCHAR(50) NOT NULL,
                `ip_address` VARCHAR(45) NOT NULL,
                `attempt_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_pseudo_ip` (`pseudo`, `ip_address`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
            $this->manager->getConnexion()->exec($sql);
        } catch (PDOException $e) {
            if (function_exists('logError')) {
                logError("Impossible de vérifier/créer login_attempts : " . $e->getMessage());
            }
        }
    }

    public function recordAttempt(string $pseudo, string $ip): void
    {
        try {
            $sql = "INSERT INTO login_attempts (pseudo, ip_address, attempt_time) VALUES (:pseudo, :ip, NOW())";
            $stmt = $this->manager->getConnexion()->prepare($sql);
            $stmt->execute([':pseudo' => $pseudo, ':ip' => $ip]);
        } catch (PDOException $e) {
            logError("Erreur recordAttempt : " . $e->getMessage());
        }
    }

    public function isLockedOut(string $pseudo, string $ip): bool
    {
        try {
            $sql = "SELECT COUNT(*) FROM login_attempts 
                    WHERE (pseudo = :pseudo OR ip_address = :ip)
                    AND attempt_time > DATE_SUB(NOW(), INTERVAL " . self::LOCKOUT_TIME . " SECOND)";
            $stmt = $this->manager->getConnexion()->prepare($sql);
            $stmt->execute([':pseudo' => $pseudo, ':ip' => $ip]);
            return (int)$stmt->fetchColumn() >= self::MAX_ATTEMPTS;
        } catch (PDOException $e) {
            logError("Erreur isLockedOut : " . $e->getMessage());
            return false;
        }
    }

    public function getRemainingAttempts(string $pseudo, string $ip): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM login_attempts 
                    WHERE (pseudo = :pseudo OR ip_address = :ip)
                    AND attempt_time > DATE_SUB(NOW(), INTERVAL " . self::LOCKOUT_TIME . " SECOND)";
            $stmt = $this->manager->getConnexion()->prepare($sql);
            $stmt->execute([':pseudo' => $pseudo, ':ip' => $ip]);
            $attempts = (int)$stmt->fetchColumn();
            return max(0, self::MAX_ATTEMPTS - $attempts);
        } catch (PDOException $e) {
            return self::MAX_ATTEMPTS;
        }
    }

    public function cleanupOldAttempts(): void
    {
        try {
            $sql = "DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL " . self::LOCKOUT_TIME . " SECOND)";
            $this->manager->getConnexion()->exec($sql);
        } catch (PDOException $e) {
            logError("Erreur cleanupOldAttempts : " . $e->getMessage());
        }
    }
}
