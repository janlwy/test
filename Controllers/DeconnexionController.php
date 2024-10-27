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
}

?>
