<?php

namespace Models;

use PDO;
use Exception;
use InvalidArgumentException;

class AudioRepository implements IRepository
{
    private Manager $manager;

    public function __construct()
    {
        $this->manager = new Manager();
    }

    public function findById(int $id): ?Audio
    {
        $data = $this->manager->readTableOne('audio', $id);
        return $data ? new Audio($data) : null;
    }

    public function findAll(): array
    {
        $rows = $this->manager->readTableAll('audio');
        return array_map(fn($row) => new Audio($row), $rows);
    }

    public function findAllByUser(int $userId): array
    {
        try {
            $connection = $this->manager->getConnexion();
            $query = "SELECT * FROM audio WHERE user_id = :user_id ORDER BY id DESC";
            $stmt = $connection->prepare($query);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $audios = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $audios[] = new Audio($data);
            }
            return $audios;
        } catch (Exception $e) {
            logError("Erreur lors de la récupération des audios: " . $e->getMessage());
            return [];
        }
    }

    public function save($entity): void
    {
        if (!$entity instanceof Audio) {
            throw new InvalidArgumentException("L'entité doit être de type Audio");
        }

        $data = [
            'title' => $entity->getTitle(),
            'artist' => $entity->getArtist(),
            'image' => $entity->getImage() ?: 'default_cover.png',
            'path' => $entity->getPath(),
            'user_id' => $entity->getUserId()
        ];

        if ($entity->getId()) {
            $this->manager->updateTable('audio', $data, $entity->getId());
        } else {
            $result = $this->manager->insertTable('audio', $data);
            if (isset($result['id'])) {
                $entity->hydrate(['id' => $result['id']]);
            }
        }
    }

    public function delete(int $id): void
    {
        $audio = $this->findById($id);
        if (!$audio) {
            throw new Exception("Audio non trouvé");
        }

        // Suppression des fichiers physiques sur le disque
        $audioFile = $audio->getFullPath();
        if (file_exists($audioFile) && is_file($audioFile)) {
            @unlink($audioFile);
        }

        $imageName = $audio->getImage();
        if (!empty($imageName) && $imageName !== 'default_cover.png') {
            $imageFile = 'Ressources/images/pochettes/' . $imageName;
            if (file_exists($imageFile) && is_file($imageFile)) {
                @unlink($imageFile);
            }
        }

        $this->manager->deleteTable('audio', $id);
    }

    public function beginTransaction(): void
    {
        $this->manager->beginTransaction();
    }

    public function commit(): void
    {
        $this->manager->commit();
    }

    public function rollback(): void
    {
        $this->manager->rollback();
    }

    public function count(): int
    {
        $connection = $this->manager->getConnexion();
        $stmt = $connection->query("SELECT COUNT(*) FROM audio");
        return (int) $stmt->fetchColumn();
    }
}
