<?php

class Router
{
    public function __construct()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : 'accueil/index';
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
            array_splice($urls, 0, 1); // Retire le nom du contrôleur
        }


        $session = SessionManager::getInstance();
        $session->startSession();
        $session->ensureCsrfToken();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$session->validateToken($_POST['csrf_token'] ?? null)) {
                logError("Erreur CSRF : jeton invalide.");
                header('Location: ?url=connexion/index');
                exit();
            }
        }
            
        logInfo("Tentative de chargement du contrôleur : $controllerName et de la méthode : $actionName");
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $actionName)) {
                    logInfo("Appel de la méthode $actionName dans le contrôleur $controllerName");
                    // Pass any additional URL parameters to the method
                    $params = array_slice($urls, 2);
                    call_user_func_array([$controller, $actionName], $params);
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
