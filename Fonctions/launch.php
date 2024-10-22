<?php

function charger($class)
{
	$fileModels = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Models" . DIRECTORY_SEPARATOR . "$class.php";
	$fileControllers = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Controllers" . DIRECTORY_SEPARATOR . "$class.php";
	$fileViews = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Views" . DIRECTORY_SEPARATOR . "$class.php";
	$fileRessources = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Ressources" . DIRECTORY_SEPARATOR . "$class.php";
	$fileFonctions = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Fonctions" . DIRECTORY_SEPARATOR . "$class.php";
	$files = array($fileModels, $fileControllers, $fileViews, $fileRessources, $fileFonctions);
	foreach ($files as $file) {
		if (file_exists($file)) {
			include_once $file; // Utilisation de include_once
		}
	}
}

function printr($tableaux)
{
	echo "<h1><pre>";
	print_r($tableaux);
	echo "</pre>-----------------</h1>";
	//die;
}

function generate($file, $datas, $layout = "Views/base.html.php")
{
	$content = generateFile($file, $datas);
	echo generateFile($layout, array('content' => $content)); // Afficher le contenu généré
}

function generateFile($file, $datas)
{
	if (file_exists($file)) {
		ob_start();
		extract($datas);
		require_once $file;
		return ob_get_clean();
	} else {
		echo "Le fichier $file n'existe pas!";
		die;
	}
}

function logError($message, $level = 'error')
{
	$logFile = 'errors.log'; // Nom du fichier de log
	$logMessage = date('Y-m-d H:i:s') . " - [$level] - $message\n";
	file_put_contents($logFile, $logMessage, FILE_APPEND);
}
?>
