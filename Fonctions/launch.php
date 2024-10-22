<?php

function charger($class)
{
	$fileModels = "Models/$class.php";
	$fileControllers = "Controllers/$class.php";
	$fileViews = "Views/$class.php";
	$fileRessources = "Ressources/$class.php";
	$fileFonctions = "Fonctions/$class.php";
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
