<?php

class DeconnexionController
{
    public function index()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Détruire la session pour déconnecter l'utilisateur
        session_unset();
        session_destroy();

        // Rediriger vers la page d'accueil
        header('Location: ?url=accueil/index');
        exit();
    }
}

?>
