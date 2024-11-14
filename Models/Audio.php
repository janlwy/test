<?php

require_once 'AbstractModel.php';
require_once 'Manager.php';

class Audio extends AbstractModel
{
    private $title;
    private $artist;
    private $image;
    private $path;

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }

    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getArtist()
    {
        return $this->artist;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    private $user_id;
    private $created_at;

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getUserId()
    {
        return $this->user_id;
    }

    public function save()
    {
        $manager = new Manager();
        try {
            $data = [
                'title' => $this->title,
                'artist' => $this->artist,
                'image' => $this->image,
                'path' => $this->path,
                'user_id' => $this->user_id
            ];

            if ($this->id) {
                $manager->updateTable('audio', $data, $this->id);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $result = $manager->insertTable('audio', $data);
                $this->id = $result['id'];
            }

            return true;
        } catch (Exception $e) {
            logError("Erreur lors de la sauvegarde audio: " . $e->getMessage());
            return false;
        }
    }

    public function validateAudioFile($file)
    {
        $allowedTypes = [
            'audio/mpeg' => 'mp3',
            'audio/mp4' => 'm4a',
            'audio/wav' => 'wav',
            'audio/x-m4a' => 'm4a',
            'audio/aac' => 'aac',
            'audio/ogg' => 'ogg'
        ];

        $errors = [];
        
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $errors[] = "Aucun fichier audio n'a été uploadé";
            return $errors;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!array_key_exists($mimeType, $allowedTypes)) {
            $errors[] = "Type de fichier non autorisé. Types acceptés: MP3, M4A, WAV, AAC, OGG";
        }

        if ($file['size'] > 50 * 1024 * 1024) { // 50MB
            $errors[] = "Le fichier est trop volumineux (max 50MB)";
        }

        return $errors;
    }

    public function getFullPath()
    {
        return 'Ressources/audio/' . $this->path;
    }

    public function getFullImagePath()
    {
        return 'Ressources/images/pochettes/' . $this->image;
    }

    public function delete()
    {
        try {
            // Supprimer les fichiers physiques
            $audioPath = $this->getFullPath();
            $imagePath = $this->getFullImagePath();

            if (file_exists($audioPath)) {
                unlink($audioPath);
            }
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Supprimer l'enregistrement de la base de données
            $manager = new Manager();
            return $manager->deleteFromTable('audio', $this->id);
        } catch (Exception $e) {
            logError("Erreur lors de la suppression audio: " . $e->getMessage());
            return false;
        }
    }

    public static function find($id)
    {
        $manager = new Manager();
        $data = $manager->readTableOne('audio', $id);
        return $data ? new self($data) : null;
    }
}

?>
