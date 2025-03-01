<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "Fonctions" . DIRECTORY_SEPARATOR . "launch.php";
spl_autoload_register('charger');

use Controllers\Router;
use Session\SessionManager;

try {
    $session = SessionManager::getInstance();
    $session->startSession();

    // Vérifier que la session est active
    if (session_status() !== PHP_SESSION_ACTIVE) {
        logError("La session n'a pas pu être démarrée dans index.php");
    }

    $router = new Router();
} catch (\Exception $e) {
    // Journaliser l'erreur
    if (function_exists('logError')) {
        logError("Erreur critique dans index.php: " . $e->getMessage());
    }
    
    // Afficher un message d'erreur convivial
    echo "Une erreur est survenue. Veuillez réessayer ultérieurement.";
}
?>

