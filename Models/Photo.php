<?php

class Photo extends AbstractModel {
    private $title;
    private $description;
    private $path;
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function setPath($path) {
        $this->path = $path;
    }
    
    public function getTitle() {
        return $this->title;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function validate(): array {
        $errors = [];
        if (empty($this->title)) {
            $errors[] = "Le titre est requis";
        }
        if (empty($this->path)) {
            $errors[] = "Le chemin de l'image est requis";
        }
        return $errors;
    }
}
