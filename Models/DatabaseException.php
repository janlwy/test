<?php

class DatabaseException extends Exception {
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
        logError("Erreur base de données : " . $message);
    }
}
