<?php
namespace Models;

use PDO;
use PDOException;
use Models\DatabaseException;

class Bdd {
    private static $instance = null;
    
    private function __construct() {
        // Constructeur privé pour empêcher l'instanciation directe
    }
    
    public static function getConnexion(): PDO {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                    DB_USER,
                    DB_PASSWORD,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                    ]
                );
            } catch (PDOException $e) {
                logError("Erreur de connexion à la base de données : " . $e->getMessage());
                throw new DatabaseException("Impossible de se connecter à la base de données", 0, $e);
            }
        }
        return self::$instance;
    }
}
