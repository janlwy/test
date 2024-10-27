<?php

class DeconnexionController extends BaseController
{
    public function index()
    {
        try {
            // Vérifier si une session existe avant de tenter de la détruire
            if (session_status() === PHP_SESSION_ACTIVE) {
                // Nettoyer proprement la session
                $this->session->destroySession();
            }
        } catch (Exception $e) {
            logError("Erreur lors de la déconnexion : " . $e->getMessage());
        } finally {
            // Rediriger vers la page d'accueil même en cas d'erreur
            header('Location: ?url=accueil/index');
            exit();
        }
    }
}

?>
