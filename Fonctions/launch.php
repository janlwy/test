<?php
use Session\SessionManager;

function charger($class)
{
    // Convert namespace separators to directory separators
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    
    // Define possible paths
    $paths = [
        "./Models/",
        "./Controllers/",
        "./Views/",
        "./Ressources/",
        "./Fonctions/",
        "./"
    ];
    
    // Try each path
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            include_once $file;
            return;
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

function generate($file, $datas, $layout = "Views/base.html.php", $pageTitle = "Calepin", $hideNav = false)
{
	$content = generateFile($file, $datas);
	// Ajouter hideNav au tableau $datas
	$datas['hideNav'] = $hideNav;
	if (!isset($datas['session'])) {
		$datas['session'] = SessionManager::getInstance();
	}
	echo generateFile($layout, array('content' => $content, 'pageTitle' => $pageTitle, 'datas' => $datas, 'session' => $datas['session']));
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

function logError(string $message, string $level = 'error'): void
{
    // Définir un répertoire de logs par défaut si non configuré
    $logDir = defined('LOG_DIR') ? LOG_DIR : __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs';
    
    // Créer le répertoire s'il n'existe pas
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0755, true);
    }
    
    $logFile = $logDir . DIRECTORY_SEPARATOR . 'application.log';
    $logMessage = sprintf(
        "[%s] %s: %s\n",
        date('Y-m-d H:i:s'),
        strtoupper($level),
        $message
    );
    
    // Rotation des logs si > 10MB
    if (file_exists($logFile) && filesize($logFile) > 10 * 1024 * 1024) {
        rename($logFile, $logFile . '.' . date('YmdHis'));
    }
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

function logInfo(string $message): void
{
    logError($message, 'info');
}

function logDebug(string $message): void
{
    if (defined('DEBUG_MODE') && DEBUG_MODE) {
        logError($message, 'debug');
    }
}
function shouldDisplayNav($datas) {
    return !isset($datas['hideNav']) || !$datas['hideNav'];
}

