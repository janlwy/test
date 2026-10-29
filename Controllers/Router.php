<?php

namespace Controllers;

use Session\SessionManager;

class Router
{
    public function __construct()
    {
        $rawUrl = $_GET['url'] ?? 'accueil/index';
        // Nettoyage strict de l'URL de routage uniquement (sans altérer $_POST)
        $url = trim(filter_var($rawUrl, FILTER_SANITIZE_URL), '/');
        if (empty($url)) {
            $url = 'accueil/index';
        }

        $urls = explode('/', $url);
        $params = [];

        if (count($urls) >= 2 && strtolower($urls[0]) === 'admin') {
            $controllerName = ucfirst($urls[1]) . "Controller";
            $controllerPath = __DIR__ . "/Admin/" . $controllerName . ".php";
            $actionName = $urls[2] ?? 'index';
            $params = array_slice($urls, 3);

            if (!file_exists($controllerPath)) {
                $controllerPath = __DIR__ . "/" . $controllerName . ".php";
            }
        } else {
            $controllerName = ucfirst($urls[0]) . "Controller";
            $controllerPath = __DIR__ . "/" . $controllerName . ".php";
            $actionName = $urls[1] ?? 'index';
            $params = array_slice($urls, 2);
        }

        $session = SessionManager::getInstance();
        $session->startSession();
        if (session_status() === PHP_SESSION_ACTIVE) {
            $session->ensureCsrfToken();
        }

        $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

        $csrfProtectedActions = ['create', 'update', 'delete', 'save', 'connect', 'createUser'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($actionName, $csrfProtectedActions, true)) {
            $token = $isAjax
                ? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? null))
                : ($_POST['csrf_token'] ?? null);

            if (!$session->validateToken($token)) {
                logError("Erreur CSRF : jeton invalide pour l'action $actionName");
                if ($isAjax) {
                    header('Content-Type: application/json');
                    http_response_code(403);
                    echo json_encode(['error' => true, 'message' => 'Token CSRF invalide']);
                    exit();
                }

                $_SESSION['erreur'] = "Erreur de sécurité (jeton expiré ou invalide). Veuillez réessayer.";
                if (strpos($url, 'creation') !== false) {
                    header('Location: ?url=creation/createUserForm');
                } elseif (strpos($url, 'connexion') !== false) {
                    header('Location: ?url=connexion/index');
                } else {
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '?url=accueil/index'));
                }
                exit();
            }
        }

        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            $fullClassName = "Controllers\\$controllerName";
            if (class_exists($fullClassName)) {
                $controller = new $fullClassName();
                if (method_exists($controller, $actionName)) {
                    call_user_func_array([$controller, $actionName], $params);
                } else {
                    http_response_code(404);
                    echo "La méthode $actionName n'existe pas dans le contrôleur $controllerName.";
                }
            } else {
                http_response_code(404);
                echo "Le contrôleur $controllerName n'existe pas.";
            }
        } else {
            http_response_code(404);
            echo "Le fichier du contrôleur $controllerName n'existe pas.";
        }
    }
}
