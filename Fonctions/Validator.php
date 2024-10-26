<?php

class Validator {
    private $errors = [];
    
    public function validate($data, $rules) {
        foreach ($rules as $field => $fieldRules) {
            if (!isset($data[$field]) && in_array('required', $fieldRules)) {
                $this->addError($field, 'Ce champ est requis');
                continue;
            }
            
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $rule) {
                if (is_string($rule)) {
                    $this->validateRule($field, $value, $rule);
                } elseif (is_array($rule)) {
                    $this->validateParameterizedRule($field, $value, $rule);
                }
            }
        }
        
        return empty($this->errors);
    }
    
    private function validateRule($field, $value, $rule) {
        switch ($rule) {
            case 'required':
                if (empty($value)) {
                    $this->addError($field, 'Ce champ est requis');
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'Email invalide');
                }
                break;
            case 'alpha':
                if (!preg_match('/^[a-zA-Z]+$/', $value)) {
                    $this->addError($field, 'Seules les lettres sont autorisées');
                }
                break;
            case 'alphanumeric':
                if (!preg_match('/^[a-zA-Z0-9]+$/', $value)) {
                    $this->addError($field, 'Seuls les caractères alphanumériques sont autorisés');
                }
                break;
            case 'username':
                if (!preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $value)) {
                    $this->addError($field, 'Le nom d\'utilisateur doit contenir entre 3 et 20 caractères (lettres, chiffres, - et _)');
                }
                break;
            case 'password':
                if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$/', $value)) {
                    $this->addError($field, 'Le mot de passe doit contenir au moins 8 caractères, une lettre, un chiffre et un caractère spécial');
                }
                break;
        }
    }
    
    private function validateParameterizedRule($field, $value, $rule) {
        $ruleName = $rule[0];
        $parameter = $rule[1];
        
        switch ($ruleName) {
            case 'min':
                if (strlen($value) < $parameter) {
                    $this->addError($field, "Minimum $parameter caractères requis");
                }
                break;
            case 'max':
                if (strlen($value) > $parameter) {
                    $this->addError($field, "Maximum $parameter caractères autorisés");
                }
                break;
            case 'matches':
                if ($value !== $parameter) {
                    $this->addError($field, 'Les valeurs ne correspondent pas');
                }
                break;
            case 'mime':
                if (!in_array($value, $parameter)) {
                    $this->addError($field, 'Type de fichier non autorisé');
                }
                break;
            case 'maxsize':
                if ($value > $parameter) {
                    $this->addError($field, 'Fichier trop volumineux');
                }
                break;
        }
    }
    
    private function addError($field, $message) {
        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function hasErrors() {
        return !empty($this->errors);
    }
    
    public function getFirstError() {
        if (!$this->hasErrors()) {
            return null;
        }
        $firstField = array_key_first($this->errors);
        return $this->errors[$firstField][0];
    }
}
