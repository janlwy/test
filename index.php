<?php
session_start(); // Démarrer la session au début du point d'entrée

require_once __DIR__ . DIRECTORY_SEPARATOR . "Fonctions" . DIRECTORY_SEPARATOR . "launch.php";
spl_autoload_register('charger');

require_once __DIR__ . DIRECTORY_SEPARATOR . "hello.php";

$router = new Router();
hello();


?>
