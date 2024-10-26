<?php

class DeconnexionController
{
    public function index()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        startSessionIfNeeded();

        // Utiliser la fonction destroySession pour nettoyer proprement la session
        destroySession();

        // Rediriger vers la page d'accueil
        header('Location: ?url=accueil/index');
        exit();
    }
}

?>
