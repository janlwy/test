<?php

class DeconnexionController extends BaseController
{
    public function index()
    {
        // Nettoyer proprement la session
        $this->session->destroySession();

        // Rediriger vers la page d'accueil
        header('Location: ?url=accueil/index');
        exit();
    }

    public function list()
    {
        // Non implémenté pour ce contrôleur
    }

    public function create()
    {
        // Non implémenté pour ce contrôleur
    }

    public function update()
    {
        // Non implémenté pour ce contrôleur
    }

    public function delete()
    {
        // Non implémenté pour ce contrôleur
    }
}

?>
