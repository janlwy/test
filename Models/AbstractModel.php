<?php

abstract class AbstractModel {
    protected $id;
    protected $created_at;
    protected $user_id;
    
    public function __construct(array $data = []) {
        $this->hydrate($data);
    }
    
    public function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }
    
    public function getId(): ?int {
        return $this->id;
    }
    
    public function getCreatedAt(): ?string {
        return $this->created_at;
    }
    
    public function getUserId(): ?int {
        return $this->user_id;
    }
    
    protected function setId($id): void {
        $this->id = (int)$id;
    }
    
    protected function setCreatedAt($created_at): void {
        $this->created_at = $created_at;
    }
    
    protected function setUserId($user_id): void {
        $this->user_id = (int)$user_id;
    }
    
    public function toArray(): array {
        return [
            'id' => $this->getId(),
            'created_at' => $this->getCreatedAt(),
            'user_id' => $this->getUserId()
        ];
    }
}
