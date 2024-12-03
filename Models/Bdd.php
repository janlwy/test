<?php

class Bdd
{
    public static function getConnexion()
    {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        try {
            $connexion = new PDO($dsn, DB_USER, DB_PASSWORD);
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connexion;
        } catch (PDOException $e) {
            logError("Erreur de connexion à la base de données : " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez vérifier les paramètres de connexion.");
        }
    }
}
?>
