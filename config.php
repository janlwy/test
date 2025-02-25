<?php
// Configuration de la base de données
$dbHost = getenv('DB_HOST') ?: 'localhost'; // Utilise localhost par défaut si DB_HOST n'est pas défini

define('DB_HOST', $dbHost);
define('DB_NAME', 'cda_projet');
define('DB_USER', 'root');
define('DB_PASSWORD', '');

// Vérification de la connexion
try {
    $testConn = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
        ]
    );
} catch (PDOException $e) {
    logError("Erreur de connexion à la base de données : " . $e->getMessage());
    throw new RuntimeException("Impossible de se connecter à la base de données. Vérifiez vos paramètres de connexion.");
}

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
