<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . "Fonctions" . DIRECTORY_SEPARATOR . "launch.php";
spl_autoload_register('charger');

$session = SessionManager::getInstance();
$session->startSession();

$router = new Router();
?>

