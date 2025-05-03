<?php
namespace Models;

use PDO;
use PDOException;
use Models\DatabaseException;

class Bdd {
    private static $instance = null;
    private static $attempts = 0;
    private static $maxAttempts = 3;
    
    private function __construct() {
        // Constructeur privé pour empêcher l'instanciation directe
    }
    
    /**
     * Empêche le clonage de l'instance
     */
    private function __clone() {}
    
    /**
     * Empêche la désérialisation de l'instance
     */
    public function __wakeup() {
        throw new DatabaseException("Impossible de désérialiser une instance de singleton");
    }
    
    /**
     * Obtient une connexion à la base de données
     * 
     * @return PDO Instance de PDO
     * @throws DatabaseException Si la connexion échoue
     */
    public static function getConnexion(): PDO {
        if (self::$instance === null) {
            try {
                // Vérifier si les constantes de configuration sont définies
                if (!defined('DB_HOST') || !defined('DB_NAME') || !defined('DB_USER') || !defined('DB_PASSWORD')) {
                    throw new DatabaseException("Configuration de base de données manquante");
                }
                
                // Limiter les tentatives de connexion
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
                        PDO::ATTR_EMULATE_PREPARES => false,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                    ]
                );
                
                // Réinitialiser le compteur de tentatives après une connexion réussie
                self::$attempts = 0;
            } catch (PDOException $e) {
                logError("Erreur de connexion à la base de données : " . $e->getMessage());
                throw new DatabaseException("Impossible de se connecter à la base de données", 0, $e);
            }
        }
        return self::$instance;
    }
    
    /**
     * Ferme la connexion à la base de données
     */
    public static function closeConnection(): void {
        self::$instance = null;
    }
    
    /**
     * Vérifie si la connexion est active
     * 
     * @return bool True si la connexion est active
     */
    public static function isConnected(): bool {
        return self::$instance !== null;
    }
}
