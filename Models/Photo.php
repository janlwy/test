<?php

namespace Models;

class Photo extends AbstractModel
{
    private ?string $title = null;
    private ?string $description = null;
    private ?string $ocr_text = null;
    private ?string $path = null;

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setDescription($description): void
    {
        $this->description = $description;
    }

    public function setOcrText($ocr_text): void
    {
        $this->ocr_text = $ocr_text;
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

    public function getOcrText(): ?string
    {
        return $this->ocr_text;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getFullPath(): string
    {
        return 'Ressources/images/photos/' . $this->path;
    }

    public function validate(): array
    {
        $errors = [];
        if (empty($this->title)) {
            $errors[] = "Le titre est requis";
        }
        if (empty($this->path)) {
            $errors[] = "Le fichier image est requis";
        }
        return $errors;
    }
}
