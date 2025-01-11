<?php
namespace App\Models;

use App\Models\Manager;

class TexteRepository implements IRepository {
    private $manager;
    
    public function __construct() {
        $this->manager = new Manager();
    }
    
    public function findById(int $id) {
        return $this->manager->readTableOne('texte', $id);
    }
    
    public function findAll(): array {
        return $this->manager->readTableAll('texte');
    }
    
    public function findAllByUser(int $userId): array {
        return $this->manager->readTableAll('texte', $userId);
    }
    
    public function save($entity): void {
        if ($entity->getId()) {
            $this->manager->updateTable('texte', $entity->toArray(), $entity->getId());
        } else {
            $this->manager->insertTable('texte', $entity->toArray());
        }
    }
    
    public function delete(int $id): void {
        $this->manager->deleteTable('texte', $id);
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
        $stmt = $connection->query("SELECT COUNT(*) FROM texte");
        return (int) $stmt->fetchColumn();
    }
}
