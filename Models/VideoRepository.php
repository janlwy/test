<?php

namespace Models;

use PDO;
use Exception;
use InvalidArgumentException;

class VideoRepository implements IRepository
{
    private Manager $manager;

    public function __construct()
    {
        $this->manager = new Manager();
    }

    public function findById(int $id): ?Video
    {
        $data = $this->manager->readTableOne('video', $id);
        return $data ? new Video($data) : null;
    }

    public function findAll(): array
    {
        $rows = $this->manager->readTableAll('video');
        return array_map(fn($row) => new Video($row), $rows);
    }

    public function findAllByUser(int $userId): array
    {
        try {
            $connection = $this->manager->getConnexion();
            $query = "SELECT * FROM video WHERE user_id = :user_id ORDER BY created_at DESC, id DESC";
            $stmt = $connection->prepare($query);
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $videos = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $videos[] = new Video($data);
            }
            return $videos;
        } catch (Exception $e) {
            logError("Erreur lors de la récupération des vidéos: " . $e->getMessage());
            return [];
        }
    }

    /**
     * @param Video $entity
     */
    public function save(object $entity): void
    {
        if (!$entity instanceof Video) {
            throw new InvalidArgumentException("L'entité doit être de type Video");
        }

        $data = [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription() ?? '',
            'thumbnail' => $entity->getThumbnail() ?: 'default_thumb.png',
            'path' => $entity->getPath(),
            'user_id' => $entity->getUserId()
        ];

        if ($entity->getId()) {
            $this->manager->updateTable('video', $data, $entity->getId());
        } else {
            $result = $this->manager->insertTable('video', $data);
            if (isset($result['id'])) {
                $entity->hydrate(['id' => $result['id']]);
            }
        }
    }

    public function delete(int $id): void
    {
        $video = $this->findById($id);
        if (!$video) {
            throw new Exception("Vidéo introuvable");
        }

        $videoFile = $video->getFullPath();
        if (file_exists($videoFile) && is_file($videoFile)) {
            @unlink($videoFile);
        }

        $thumb = $video->getThumbnail();
        if (!empty($thumb) && $thumb !== 'default_thumb.png') {
            $thumbFile = 'Ressources/images/thumbnails/' . $thumb;
            if (file_exists($thumbFile) && is_file($thumbFile)) {
                @unlink($thumbFile);
            }
        }

        $this->manager->deleteTable('video', $id);
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
        $stmt = $connection->query("SELECT COUNT(*) FROM video");
        return (int) $stmt->fetchColumn();
    }
}
