<?php
namespace Models;

class DatabaseException extends \Exception {
    public function __construct($message, $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        if (function_exists('logError')) {
            logError("Erreur base de données : " . $message);
        }
    }
}
