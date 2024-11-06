<?php

class DeconnexionController extends BaseController
{
    public function index()
    {
        try {
            // Déterminer si c'est une déconnexion admin ou utilisateur standard
            $isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
            
            // Nettoyer proprement la session
            $this->session->destroySession();
            session_write_close();
            
            // Forcer la suppression du cookie de session
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            
            // Rediriger vers la page appropriée
            if ($isAdmin) {
                header('Location: index.php?url=admin/login/index', true, 302);
            } else {
                header('Location: index.php?url=connexion/index', true, 302);
            }
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
