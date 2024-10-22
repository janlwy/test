<?php
session_start(); // Démarrer la session au début du point d'entrée

require_once "Fonctions/launch.php";
spl_autoload_register('charger');

$router = new Router();


?>