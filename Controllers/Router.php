<?php
class Router
{

	public function __construct()
	{
		if (isset($_GET['url'])) {
			$url = $_GET['url'];
			$urls = explode('/', $url);
			$class = $urls[0];
		} else if (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/connexion') !== false) {
			$class = 'Connexion@connect';
		} else {
			$url = '';
			$class = 'Accueil';
		}

		$controllerAction = explode('@', $class);
		$controllerName = $controllerAction[0] . "Controller";
		$actionName = isset($controllerAction[1]) ? $controllerAction[1] : 'index'; // méthode par défaut si non spécifiée

		require_once "Controllers/$controllerName.php";
		$controller = new $controllerName(); // Instancier sans argument
		$controller->$actionName(); // Appeler la méthode connect()

	}
}

?>