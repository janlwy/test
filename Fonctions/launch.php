<?php
require_once __DIR__ . '/../config.php';

use Session\SessionManager;

function charger($class)
{
    $projectRoot = dirname(__DIR__) . DIRECTORY_SEPARATOR;
    $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

    // 1. Résolution directe PSR-4 depuis la racine (ex: Session\SessionManager -> Session/SessionManager.php)
    $directFile = $projectRoot . $relativePath;
    if (file_exists($directFile)) {
        require_once $directFile;
        return;
    }

    // 2. Fallback sur les dossiers principaux si une classe est appelée sans namespace
    $folders = ['Models', 'Controllers', 'Session', 'Fonctions'];
    foreach ($folders as $folder) {
        $file = $projectRoot . $folder . DIRECTORY_SEPARATOR . basename($relativePath);
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
}

function printr($tableaux)
{
    echo "<h1><pre>";
    print_r($tableaux);
    echo "</pre>-----------------</h1>";
}

function generate($file, $datas, $layout = "Views/base.html.php", $pageTitle = "Calepin", $hideNav = false)
{
    if (!isset($datas['session'])) {
        $datas['session'] = SessionManager::getInstance();
    }
    $datas['session']->ensureCsrfToken();
    $datas['hideNav'] = $hideNav;

    $content = generateFile($file, $datas);

    echo generateFile($layout, [
        'content' => $content,
        'pageTitle' => $pageTitle,
        'datas' => $datas,
        'session' => $datas['session']
    ]);
}

function generateFile($file, $datas)
{
    $fullPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $file);
    if (file_exists($fullPath)) {
        ob_start();
        extract($datas);
        require $fullPath;
        return ob_get_clean();
    } else {
        logError("Vue introuvable : $fullPath");
        echo "Le fichier $file n'existe pas!";
        die;
    }
}

function logError(string $message, string $level = 'error'): void
{
    $logDir = defined('LOG_DIR') ? LOG_DIR : dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs';

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

function shouldDisplayNav($datas): bool
{
    return !isset($datas['hideNav']) || !$datas['hideNav'];
}
