<?php
namespace Fonctions;

class Validator {
    private $errors = [];
    private $data = [];
    
    public function validate($data, $rules) {
        $this->data = $data;
        $this->errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            
            foreach ($fieldRules as $ruleData) {
                if (is_array($ruleData)) {
                    // Format avec message: ['rule' => 'required', 'message' => 'Ce champ est requis']
                    $rule = $ruleData['rule'] ?? $ruleData[0];
                    $message = $ruleData['message'] ?? null;
                } else {
                    // Format simple: 'required'
                    $rule = $ruleData;
                    $message = null;
                }
                
                if (is_array($rule)) {
                    // Règle avec paramètres : ['min', 3]
                    $this->validateParameterizedRule($field, $value, $rule[0], $rule[1], $message);
                } else {
                    // Règle simple : 'required'
                    $this->validateRule($field, $value, $rule, $message);
                }
            }
        }
        
        return empty($this->errors);
    }
    
    private function validateRule(string $field, $value, string $rule, ?string $message = null): void {
        // Nettoyage de la valeur si c'est une chaîne
        $value = is_string($value) ? trim($value) : $value;
        $isValid = true;
        $defaultMessage = '';
        
        switch ($rule) {
            case 'required':
                $isValid = !($value === null || $value === '');
                $defaultMessage = 'Ce champ est requis';
                
                if (!$isValid) {
                    $this->addError($field, $message ?? $defaultMessage);
                }
                return; // On sort de la fonction après avoir vérifié required
                break;
                
            case 'email':
                $isValid = filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
                $defaultMessage = 'Adresse email invalide';
                break;
                
            case 'alpha':
                $isValid = preg_match('/^[a-zA-Z]+$/', $value);
                $defaultMessage = 'Seules les lettres sont autorisées';
                break;
                
            case 'alphanumeric':
                $isValid = preg_match('/^[a-zA-Z0-9]+$/', $value);
                $defaultMessage = 'Seuls les caractères alphanumériques sont autorisés';
                break;
                
            case 'username':
                $isValid = preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $value);
                $defaultMessage = 'Le nom d\'utilisateur doit contenir entre 3 et 20 caractères (lettres, chiffres, - et _)';
                break;
                
            case 'password':
                $isValid = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,72}$/', $value);
                $defaultMessage = 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial';
                break;
        }
        
        if (!$isValid) {
            $this->addError($field, $message ?? $defaultMessage);
        }
    }
    
    private function validateParameterizedRule($field, $value, $ruleName, $parameter, $message = null) {
        // Si la valeur est vide et que ce n'est pas une règle 'required', on ne valide pas
        if (($value === null || $value === '') && $ruleName !== 'required') {
            return;
        }
        
        $isValid = true;
        $defaultMessage = '';
        
        switch ($ruleName) {
            case 'min':
                $isValid = strlen($value) >= $parameter;
                $defaultMessage = "Minimum $parameter caractères requis";
                break;
                
            case 'max':
                $isValid = strlen($value) <= $parameter;
                $defaultMessage = "Maximum $parameter caractères autorisés";
                break;
                
            case 'match':
                $matchValue = $this->data[$parameter] ?? null;
                $isValid = $value === $matchValue;
                $defaultMessage = "Les valeurs ne correspondent pas";
                break;
                
            case 'pattern':
                $isValid = preg_match($parameter, $value);
                $defaultMessage = "Format invalide";
                break;
                
            case 'mime':
                $isValid = in_array($value, (array)$parameter);
                $defaultMessage = "Type de fichier non autorisé";
                break;
                
            case 'maxsize':
                $isValid = $value <= $parameter;
                $defaultMessage = "Taille maximale dépassée";
                break;
        }
        
        if (!$isValid) {
            $this->addError($field, $message ?? $defaultMessage);
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
