<?php
namespace Fonctions;

class XssProtection {
    public static function clean($data) {
        if (is_array($data)) {
            return array_map([self::class, 'clean'], $data);
        }
        
        // Convert special characters to HTML entities
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    public static function cleanGet() {
        $_GET = self::clean($_GET);
    }
    
    public static function cleanPost() {
        $_POST = self::clean($_POST);
    }
}
