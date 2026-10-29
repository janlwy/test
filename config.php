<?php
// Configuration de la base de données (compatible XAMPP et Docker)
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'cda_projet');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : '');

// Configuration de sécurité
define('CSRF_TOKEN_EXPIRATION', 3600); // 1 heure
define('MAX_DB_NAME_LENGTH', 64);
define('MAX_COLUMN_NAME_LENGTH', 64);
define('MAX_FILE_UPLOAD_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'application/pdf']);

// Configuration des logs (dans le dossier logs/ à la racine du projet)
if (!defined('LOG_DIR')) {
    define('LOG_DIR', __DIR__ . DIRECTORY_SEPARATOR . 'logs');
    if (!is_dir(LOG_DIR)) {
        @mkdir(LOG_DIR, 0755, true);
    }
}

// Configuration du fuseau horaire
date_default_timezone_set('Europe/Paris');

// Configuration du mode de débogage
define('DEBUG_MODE', true); // À définir à false en production