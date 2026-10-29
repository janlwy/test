<?php

namespace Models;

class Audio extends AbstractModel
{
    private ?string $title = null;
    private ?string $artist = null;
    private ?string $image = null;
    private ?string $path = null;

    public function setTitle($title): void
    {
        $this->title = $title;
    }

    public function setArtist($artist): void
    {
        $this->artist = $artist;
    }

    public function setImage($image): void
    {
        $this->image = $image;
    }

    public function setPath($path): void
    {
        $this->path = $path;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function getFullPath(): string
    {
        return 'Ressources/audio/' . $this->path;
    }

    public function getFullImagePath(): string
    {
        if (!empty($this->image) && $this->image !== 'default_cover.png') {
            $candidate = 'Ressources/images/pochettes/' . $this->image;
            if (file_exists($candidate)) {
                return $candidate;
            }
        }
        // Fallback sur l'image de démarrage si présente, sinon SVG data URI
        if (file_exists('Ressources/images/startPic.png')) {
            return 'Ressources/images/startPic.png';
        }
        return "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200' viewBox='0 0 24 24' fill='%23747492'><path d='M12 14c1.66 0 3-1.34 3-3V5c0-1.66-1.34-3-3-3S9 3.34 9 5v6c0 1.66 1.34 3 3 3zm5.3-3c0 3-2.54 5.1-5.3 5.1S6.7 14 6.7 11H5c0 3.41 2.72 6.23 6 6.72V21h2v-3.28c3.28-.49 6-3.31 6-6.72h-1.7z'/></svg>";
    }
}
