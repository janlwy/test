<?php

namespace Models;

use PDO;
use Exception;
use InvalidArgumentException;

class PhotoRepository implements IRepository
{
    private Manager $manager;

    public function __construct()
    {
        $this->manager = new Manager();
    }

    public function findById(int $id): ?Photo
    {
        $data = $this->manager->readTableOne('photo', $id);
        return $data ? new Photo($data) : null;
    }

    public function findAll(): array
    {
        $rows = $this->manager->readTableAll('photo');
        return array_map(fn($row) => new Photo($row), $rows);
    }

    public function findAllByUser(int $userId): array
    {
        try {
            $connection = $this->manager->getConnexion();
            $stmt = $connection->prepare("SELECT * FROM photo WHERE user_id = :user_id ORDER BY created_at DESC, id DESC");
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $photos = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $photos[] = new Photo($row);
            }
            return $photos;
        } catch (Exception $e) {
            logError("Erreur lors de la récupération des photos: " . $e->getMessage());
            return [];
        }
    }

    public function save($entity): void
    {
        if (!$entity instanceof Photo) {
            throw new InvalidArgumentException("L'entité doit être de type Photo");
        }

        $data = [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription() ?? '',
            'ocr_text' => $entity->getOcrText() ?? '',
            'path' => $entity->getPath(),
            'user_id' => $entity->getUserId()
        ];

        if ($entity->getId()) {
            $this->manager->updateTable('photo', $data, $entity->getId());
        } else {
            $result = $this->manager->insertTable('photo', $data);
            if (isset($result['id'])) {
                $entity->hydrate(['id' => $result['id']]);
            }
        }
    }

    public function delete(int $id): void
    {
        $photo = $this->findById($id);
        if (!$photo) {
            throw new Exception("Photo introuvable");
        }

        $filePath = $photo->getFullPath();
        if (file_exists($filePath) && is_file($filePath)) {
            @unlink($filePath);
        }

        $this->manager->deleteTable('photo', $id);
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
        $stmt = $connection->query("SELECT COUNT(*) FROM photo");
        return (int) $stmt->fetchColumn();
    }
}
