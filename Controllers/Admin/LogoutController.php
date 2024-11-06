<?php

class LogoutController extends BaseController implements IController {
    public function index() {
        // Détruire la session admin
        $this->session->destroySession();
        
        // Rediriger vers la page d'accueil
        header('Location: ?url=mediabox/index');
        exit();
    }
}
