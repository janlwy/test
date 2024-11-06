<?php

class DeconnexionController extends BaseController
{
    public function index()
    {
        try {
            // Vérifier si une session existe avant de tenter de la détruire
            if (session_status() === PHP_SESSION_ACTIVE) {
                // Déterminer si c'est une déconnexion admin ou utilisateur standard
                $isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
                
                // Nettoyer proprement la session
                $this->session->destroySession();
                
                // Rediriger vers la page appropriée
                if ($isAdmin) {
                    header('Location: ?url=admin/login/index');
                } else {
                    header('Location: ?url=connexion/index');
                }
                exit();
            }
        } catch (Exception $e) {
            logError("Erreur lors de la déconnexion : " . $e->getMessage());
            // En cas d'erreur, rediriger vers la page de connexion standard
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}

?>
