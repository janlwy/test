<?php

class DeconnexionController extends BaseController
{
    public function index()
    {
        try {
            logInfo("Début de la déconnexion");
            
            // Redirection vers la page de connexion unique
            $redirectUrl = 'index.php?url=connexion/index';
            logInfo("URL de redirection prévue: " . $redirectUrl);
            
            // Nettoyer la session
            if (session_status() === PHP_SESSION_ACTIVE) {
                logInfo("Destruction de la session active");
                
                // Supprimer spécifiquement les variables de session admin
                unset($_SESSION['admin']);
                unset($_SESSION['username']);
                
                // Vider toutes les autres variables de session
                $_SESSION = array();
                
                // Supprimer le cookie de session
                if (isset($_COOKIE[session_name()])) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                    logInfo("Cookie de session supprimé");
                }
                
                // Détruire la session
                session_destroy();
                logInfo("Session détruite");
                
                // Démarrer une nouvelle session pour éviter les problèmes de redirection
                session_start();
                logInfo("Nouvelle session démarrée pour la redirection");
            }
            
            // Forcer la redirection
            logInfo("Tentative de redirection vers: " . $redirectUrl);
            header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
            header("Location: " . $redirectUrl, true, 302);
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
