<?php

abstract class BaseController {
    protected function checkAuth() {
        startSessionIfNeeded();
        
        if (!isset($_SESSION['pseudo'])) {
            header('Location: ?url=connexion/index');
            exit();
        }
    }

    protected function checkCSRF() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                logError("Erreur CSRF : jeton invalide.");
                $_SESSION['erreur'] = "Erreur de sécurité. Veuillez réessayer.";
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
