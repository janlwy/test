<?php

namespace Models;

class Video extends AbstractModel
{
    private ?string $title = null;
    private ?string $description = null;
    private ?string $thumbnail = null;
    private ?string $path = null;

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function setThumbnail($thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function setPath($path): void
    {
        $this->path = $path;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getFullPath(): string
    {
        return 'Ressources/videos/' . $this->path;
    }

    public function getThumbnailPath(): string
    {
        if (!empty($this->thumbnail) && $this->thumbnail !== 'default_thumb.png') {
            $candidate = 'Ressources/images/thumbnails/' . $this->thumbnail;
            if (file_exists($candidate)) {
                return $candidate;
            }
        }
        if (file_exists('Ressources/images/startPic.png')) {
            return 'Ressources/images/startPic.png';
        }
        return '';
    }

    public function validate(): array
    {
        $errors = [];
        if (empty($this->title)) {
            $errors[] = "Le titre est requis";
        }
        if (empty($this->path)) {
            $errors[] = "Le fichier vidéo est requis";
        }
        return $errors;
    }
}
