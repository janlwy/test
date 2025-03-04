<?php
namespace Controllers;

require_once __DIR__ . '/../Fonctions/XssProtection.php';
use Session\SessionManager;
use Fonctions\XssProtection;

class Router
{
    public function __construct()
    {
        // Nettoyer les entrées GET et POST
        XssProtection::cleanGet();
        XssProtection::cleanPost();
        
        $url = isset($_GET['url']) ? $_GET['url'] : 'accueil/index';
        $url = XssProtection::clean($url);
        $urls = explode('/', $url);
        // Gestion des sous-dossiers de contrôleurs (ex: Admin/)
        if (count($urls) >= 2 && strtolower($urls[0]) === 'admin') {
            $controllerName = ucfirst($urls[1]) . "Controller";
            $controllerPath = __DIR__ . "/Admin/" . $controllerName . ".php";
            $actionName = isset($urls[2]) ? $urls[2] : 'index';
            array_splice($urls, 0, 2); // Retire 'admin' et le nom du contrôleur
            
            // Vérifier si le fichier existe dans le dossier Admin, sinon utiliser le contrôleur standard
            if (!file_exists($controllerPath)) {
                $controllerPath = __DIR__ . "/" . $controllerName . ".php";
            }
        } else {
            $controllerName = ucfirst($urls[0]) . "Controller";
            $controllerPath = __DIR__ . "/" . $controllerName . ".php";
            $actionName = isset($urls[1]) ? $urls[1] : 'index';
            $params = array_slice($urls, 2); // Récupère tous les paramètres après le contrôleur et l'action
            array_splice($urls, 0, 1); // Retire le nom du contrôleur
        }


        try {
            $session = SessionManager::getInstance();
            $session->startSession();
            
            // Vérifier que la session est active avant d'essayer d'utiliser ensureCsrfToken
            if (session_status() === PHP_SESSION_ACTIVE) {
                $session->ensureCsrfToken();
            } else {
                logError("Impossible de démarrer la session dans le Router");
            }
        } catch (\Exception $e) {
            logError("Erreur lors de l'initialisation de la session: " . $e->getMessage());
        }
        
        // Vérifier si c'est une requête AJAX
        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
                  
        // Liste des actions qui nécessitent une vérification CSRF
        $csrfProtectedActions = ['create', 'update', 'delete', 'save', 'connect', 'createUser'];
        
        // Liste des actions exemptées de vérification CSRF
        $csrfExemptActions = ['player', 'list', 'index'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentAction = $actionName ?? '';
            
            // Vérifier si l'action nécessite une protection CSRF
            if (in_array($currentAction, $csrfProtectedActions) && !in_array($currentAction, $csrfExemptActions)) {
                // Pour les requêtes AJAX
                if ($isAjax) {
                    if (!$session->validateToken($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null)) {
                        header('Content-Type: application/json');
                        http_response_code(403);
                        echo json_encode(['error' => true, 'message' => 'Token CSRF invalide']);
                        exit();
                    }
                } else {
                    // Pour les requêtes normales
                    if (!$session->validateToken($_POST['csrf_token'] ?? null)) {
                        logError("Erreur CSRF : jeton invalide pour l'action $currentAction");
                        $_SESSION['erreur'] = "Erreur de sécurité. Veuillez réessayer.";
                        header('Location: ?url=audio/list');
                        exit();
                    }
                }
            }
        }
            
        if (function_exists('logInfo')) {
            logInfo("Tentative de chargement du contrôleur : $controllerName et de la méthode : $actionName");
        }
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $fullClassName = "Controllers\\$controllerName";
            if (class_exists($fullClassName)) {
                $controller = new $fullClassName();
                if (method_exists($controller, $actionName)) {
                    logInfo("Appel de la méthode $actionName dans le contrôleur $controllerName");
                    // Pass any additional URL parameters to the method
                    call_user_func_array([$controller, $actionName], $params ?? []);
                } else {
                    header("HTTP/1.0 404 Not Found");
                    echo "La méthode $actionName n'existe pas dans le contrôleur $controllerName.";
                }
            } else {
                header("HTTP/1.0 404 Not Found");
                echo "Le contrôleur $controllerName n'existe pas.";
            }
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Le fichier du contrôleur $controllerName n'existe pas.";
        }
    }
}

?>
