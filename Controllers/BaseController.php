<?php

abstract class BaseController implements IController {
    protected $repository;
    protected $session;
    
    public function __construct() {
        $this->session = SessionManager::getInstance();
        $this->session->startSession();
    }
    
    protected function checkAuth() {
        if (!$this->session->isAuthenticated()) {
            $this->redirect('connexion/index');
        }
    }

    protected function checkCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->session->validateToken($_POST['csrf_token'] ?? null)) {
                logError("Erreur CSRF : jeton invalide.");
                $this->session->set('erreur', "Erreur de sécurité. Veuillez réessayer.");
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }
        }
    }

    protected function redirect($url) {
        header("Location: ?url=$url");
        exit();
    }
}
?>