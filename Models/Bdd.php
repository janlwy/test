<?php

namespace Models;

use PDO;
use PDOException;

require_once __DIR__ . '/../config.php';

class Bdd
{
    private static ?PDO $instance = null;
    private static int $attempts = 0;
    private static int $maxAttempts = 3;

    private function __construct() {}
    private function __clone() {}

    public function __wakeup()
    {
        throw new DatabaseException("Impossible de désérialiser une instance de singleton");
    }

    public static function getConnexion(): PDO
    {
        if (self::$instance === null) {
            try {
                if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASSWORD')) {
                    throw new DatabaseException("Configuration de base de données manquante");
                }

                if (self::$attempts >= self::$maxAttempts) {
                    throw new DatabaseException("Nombre maximal de tentatives de connexion atteint");
                }

                self::$attempts++;

                self::$instance = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                    DB_USER,
                    DB_PASSWORD,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );

                self::$attempts = 0;
            } catch (PDOException $e) {
                if (function_exists('logError')) {
                    logError("Erreur de connexion à la base de données : " . $e->getMessage());
                }
                throw new DatabaseException("Impossible de se connecter à la base de données", 0, $e);
            }
        }
        return self::$instance;
    }

    public static function closeConnection(): void
    {
        self::$instance = null;
    }

    public static function isConnected(): bool
    {
        return self::$instance !== null;
    }
}
