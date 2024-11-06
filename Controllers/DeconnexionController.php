<?php

class DeconnexionController extends BaseController
{
    public function index()
    {
        try {
            // Déterminer si c'est une déconnexion admin ou utilisateur standard
            $isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
            
            // Préparer l'URL de redirection avant de détruire la session
            $redirectUrl = $isAdmin ? 'index.php?url=admin/login/index' : 'index.php?url=connexion/index';
            
            // Nettoyer proprement la session
            session_write_close();
            $this->session->destroySession();
            
            // Forcer la suppression du cookie de session
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
                unset($_COOKIE[session_name()]);
            }
            
            // Rediriger vers la page appropriée
            header('Location: ' . $redirectUrl, true, 302);
            exit();
        } catch (Exception $e) {
            logError("Erreur lors de la déconnexion : " . $e->getMessage());
            // En cas d'erreur, rediriger vers la page de connexion standard
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}

?>
