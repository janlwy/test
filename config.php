<?php
// Configuration de la base de données
$dbHost = getenv('DB_HOST');
if (empty($dbHost)) {
    throw new RuntimeException('DB_HOST environment variable is not set');
}

define('DB_HOST', $dbHost);
define('DB_NAME', 'cda_projet');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Configuration de sécurité
define('CSRF_TOKEN_EXPIRATION', 3600); // 1 heure
define('MAX_DB_NAME_LENGTH', 64);
define('MAX_COLUMN_NAME_LENGTH', 64);

// Configuration des logs
if (!defined('LOG_DIR')) {
    define('LOG_DIR', __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'logs');
    if (!is_dir(LOG_DIR)) {
        @mkdir(LOG_DIR, 0755, true);
    }
}
?>
