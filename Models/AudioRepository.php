<?php

class AudioRepository implements IRepository {
    private $manager;
    
    public function __construct() {
        $this->manager = new Manager();
    }
    
    public function findById(int $id): ?Audio {
        $data = $this->manager->readTableOne('audio', $id);
        return $data ? new Audio($data) : null;
    }
    
    public function findAll(): array {
        return $this->findAllByUser(null);
    }
    
    public function findAllByUser(int $userId): array {
        try {
            $results = $this->manager->readTableAll('audio', $userId);
            $audios = [];
            foreach ($results as $data) {
                $audios[] = new Audio($data);
            }
            return $audios;
        } catch (Exception $e) {
            logError("Erreur lors de la récupération des audios: " . $e->getMessage());
            return [];
        }
    }
    
    public function save($entity): void {
        if (!$entity instanceof Audio) {
            throw new InvalidArgumentException("L'entité doit être de type Audio");
        }
        
        $data = [
            'title' => $entity->getTitle(),
            'artist' => $entity->getArtist(),
            'image' => $entity->getImage(),
            'path' => $entity->getPath(),
            'user_id' => $entity->getUserId()
        ];
        
        try {
            if ($entity->getId()) {
                $this->manager->updateTable('audio', $data, $entity->getId());
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $result = $this->manager->insertTable('audio', $data);
                $entity->hydrate(['id' => $result['id']]);
            }
        } catch (Exception $e) {
            logError("Erreur lors de la sauvegarde de l'audio: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function delete(int $id): void {
        try {
            $this->beginTransaction();
            $audio = $this->findById($id);
            if (!$audio) {
                throw new Exception("Audio non trouvé");
            }
            $this->manager->deleteTable('audio', $id);
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            logError("Erreur lors de la suppression de l'audio: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function beginTransaction(): void {
        $this->manager->getConnexion()->beginTransaction();
    }
    
    public function commit(): void {
        $this->manager->getConnexion()->commit();
    }
    
    public function rollback(): void {
        $this->manager->getConnexion()->rollBack();
    }
    
    public function count(): int {
        $connection = $this->manager->getConnexion();
        $stmt = $connection->query("SELECT COUNT(*) FROM audio");
        return (int) $stmt->fetchColumn();
    }
}