<?php
namespace Models;

class DatabaseException extends \Exception {
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        if (function_exists('logError')) {
            logError("Erreur base de données : " . $message);
        }
    }
}
