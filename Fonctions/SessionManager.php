<?php

function startSessionIfNeeded() {
    if (session_status() === PHP_SESSION_NONE) {
        // Paramètres de sécurité pour les cookies de session
        $cookieParams = session_get_cookie_params();
        session_set_cookie_params([
            'lifetime' => $cookieParams['lifetime'],
            'path' => '/',
            'domain' => '',
            'secure' => true, // Requiert HTTPS
            'httponly' => true, // Empêche l'accès via JavaScript
            'samesite' => 'Strict' // Protection CSRF
        ]);
        
        session_start();
        
        // Régénérer l'ID de session périodiquement pour éviter la fixation de session
        if (!isset($_SESSION['last_regeneration']) || 
            (time() - $_SESSION['last_regeneration']) > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
        
        // Vérifier le cookie de session
        if (!isset($_COOKIE[session_name()])) {
            logError("Cookie de session manquant");
            session_destroy();
            header('Location: ?url=connexion/index');
            exit();
        }
    }
}

function destroySession() {
    $_SESSION = array();
    
    // Détruire le cookie de session
    if (isset($_COOKIE[session_name()])) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}
