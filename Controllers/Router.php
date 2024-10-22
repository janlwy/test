<?php

class Router
{
    public function __construct()
    {
        $url = isset($_GET['url']) ? $_GET['url'] : 'accueil/index';
        $urls = explode('/', $url);
        $controllerName = ucfirst($urls[0]) . "Controller";
        $actionName = isset($urls[1]) ? $urls[1] : 'index';

        $controllerPath = __DIR__ . "/../Controllers/$controllerName.php";
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            if (class_exists($controllerName)) {
                $controller = new $controllerName();
                if (method_exists($controller, $actionName)) {
                    $controller->$actionName();
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
