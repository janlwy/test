<?php

class PhotoRepository implements IRepository {
    private $manager;
    
    public function __construct() {
        $this->manager = new Manager();
    }
    
    public function findById(int $id) {
        return $this->manager->readTableOne('photo', $id);
    }
    
    public function findAll(): array {
        return $this->manager->readTableAll('photo');
    }
    
    public function findAllByUser(int $userId): array {
        return $this->manager->readTableAll('photo', $userId);
    }
    
    public function save($entity): void {
        if ($entity->getId()) {
            $this->manager->updateTable('photo', $entity->toArray(), $entity->getId());
        } else {
            $this->manager->insertTable('photo', $entity->toArray());
        }
    }
    
    public function delete(int $id): void {
        $this->manager->deleteTable('photo', $id);
    }
    
    public function beginTransaction(): void {
        $this->manager->beginTransaction();
    }
    
    public function commit(): void {
        $this->manager->commit();
    }
    
    public function rollback(): void {
        $this->manager->rollback();
    }
    
    public function count(): int {
        $connection = $this->manager->getConnexion();
        $stmt = $connection->query("SELECT COUNT(*) FROM photo");
        return (int) $stmt->fetchColumn();
    }
}
