<?php

class Video extends AbstractModel {
    private $title;
    private $description;
    private $thumbnail;
    private $path;
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }
    
    public function setThumbnail($thumbnail) {
        $this->thumbnail = $thumbnail;
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
    
    public function getThumbnail() {
        return $this->thumbnail;
    }
    
    public function getPath() {
        return $this->path;
    }
    
    public function getFullPath() {
        return 'Ressources/videos/' . $this->path;
    }
    
    public function getThumbnailPath() {
        return 'Ressources/images/thumbnails/' . $this->thumbnail;
    }
    
    public function validate(): array {
        $errors = [];
        if (empty($this->title)) {
            $errors[] = "Le titre est requis";
        }
        if (empty($this->path)) {
            $errors[] = "Le chemin de la vidéo est requis";
        }
        return $errors;
    }
}
