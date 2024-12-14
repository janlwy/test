<?php

class Texte extends AbstractModel {
    private $title;
    private $content;
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getContent() {
        return $this->content;
    }
    
    public function validate(): array {
        $errors = [];
        if (empty($this->title)) {
            $errors[] = "Le titre est requis";
        }
        if (empty($this->content)) {
            $errors[] = "Le contenu est requis";
        }
        return $errors;
    }
}
