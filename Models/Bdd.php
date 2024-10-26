<?php

class Bdd
{
    public static function getConnexion()
    {
        $dsn = "mysql:host=localhost;dbname=cda_projet;charset=utf8";
        try {
            $connexion = new PDO($dsn, 'root', '');
            $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connexion;
        } catch (PDOException $e) {
            logError("Erreur de connexion à la base de données : " . $e->getMessage());
            logError("Erreur de connexion à la base de données : " . $e->getMessage());
            die("Erreur de connexion à la base de données. Veuillez vérifier les paramètres de connexion.");
        }
    }
}
?>
