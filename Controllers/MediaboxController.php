<?php

class MediaboxController
{
    public function index()
    {
        // Démarrer la session si elle n'est pas déjà démarrée
        startSessionIfNeeded();

        // Vérifier si l'utilisateur est connecté
        if (isset($_SESSION['pseudo'])) {
            $datas = [];
            generate("Views/main/mediabox.php", $datas,"Views/base.html.php");
        } else {
            // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}

?>
