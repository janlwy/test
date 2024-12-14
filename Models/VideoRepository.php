<?php

class VideoRepository implements IRepository {
    private $manager;
    
    public function __construct() {
        $this->manager = new Manager();
    }
    
    public function findById(int $id) {
        $data = $this->manager->readTableOne('video', $id);
        return $data ? new Video($data) : null;
    }
    
    public function findAll(): array {
        return $this->findAllByUser(null);
    }
    
    public function findAllByUser(int $userId): array {
        try {
            $connection = $this->manager->getConnexion();
            $query = "SELECT * FROM video WHERE user_id = :user_id ORDER BY title ASC";
            $stmt = $connection->prepare($query);
            $stmt->bindValue(':user_id', intval($userId), PDO::PARAM_INT);
            $stmt->execute();
            
            $videos = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $video = new Video($data);
                $video->fullPath = 'Ressources/videos/' . $video->getPath();
                $video->thumbnailPath = 'Ressources/images/thumbnails/' . $video->getThumbnail();
                $videos[] = $video;
            }
            return $videos;
        } catch (Exception $e) {
            logError("Erreur lors de la récupération des vidéos: " . $e->getMessage());
            return [];
        }
    }
    
    public function save($entity): void {
        if (!$entity instanceof Video) {
            throw new InvalidArgumentException("L'entité doit être de type Video");
        }
        
        $data = [
            'title' => $entity->getTitle(),
            'description' => $entity->getDescription(),
            'thumbnail' => $entity->getThumbnail(),
            'path' => $entity->getPath(),
            'user_id' => $entity->getUserId()
        ];
        
        try {
            if ($entity->getId()) {
                $this->manager->updateTable('video', $data, $entity->getId());
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $result = $this->manager->insertTable('video', $data);
                $entity->hydrate(['id' => $result['id']]);
            }
        } catch (Exception $e) {
            logError("Erreur lors de la sauvegarde de la vidéo: " . $e->getMessage());
            throw $e;
        }
    }
    
    public function delete(int $id): void {
        try {
            $this->beginTransaction();
            $video = $this->findById($id);
            if (!$video) {
                throw new Exception("Vidéo non trouvée");
            }
            $this->manager->deleteTable('video', $id);
            $this->commit();
        } catch (Exception $e) {
            $this->rollback();
            logError("Erreur lors de la suppression de la vidéo: " . $e->getMessage());
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
        $stmt = $connection->query("SELECT COUNT(*) FROM video");
        return (int) $stmt->fetchColumn();
    }
}
