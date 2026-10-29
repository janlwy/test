<?php

namespace Models;

use PDO;
use Exception;
use InvalidArgumentException;

class TexteRepository implements IRepository
{
    private Manager $manager;
    private const TABLE = 'text';

    public function __construct()
    {
        $this->manager = new Manager();
    }

    public function findById(int $id): ?Texte
    {
        $data = $this->manager->readTableOne(self::TABLE, $id);
        return $data ? new Texte($data) : null;
    }

    public function findAll(): array
    {
        $rows = $this->manager->readTableAll(self::TABLE);
        return array_map(fn($row) => new Texte($row), $rows);
    }

    public function findAllByUser(int $userId): array
    {
        try {
            $connection = $this->manager->getConnexion();
            $stmt = $connection->prepare("SELECT * FROM `text` WHERE user_id = :user_id ORDER BY created_at DESC, id DESC");
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            $textes = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $textes[] = new Texte($row);
            }
            return $textes;
        } catch (Exception $e) {
            logError("Erreur lors de la récupération des textes: " . $e->getMessage());
            return [];
        }
    }

    public function save($entity): void
    {
        if (!$entity instanceof Texte) {
            throw new InvalidArgumentException("L'entité doit être de type Texte");
        }

        $data = [
            'title' => $entity->getTitle(),
            'content' => $entity->getContent(),
            'user_id' => $entity->getUserId()
        ];

        if ($entity->getId()) {
            $this->manager->updateTable(self::TABLE, $data, $entity->getId());
        } else {
            $result = $this->manager->insertTable(self::TABLE, $data);
            if (isset($result['id'])) {
                $entity->hydrate(['id' => $result['id']]);
            }
        }
    }

    public function delete(int $id): void
    {
        $this->manager->deleteTable(self::TABLE, $id);
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
        $stmt = $connection->query("SELECT COUNT(*) FROM `text`");
        return (int) $stmt->fetchColumn();
    }
}
