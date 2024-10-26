<?php

abstract class AbstractModel {
    protected $id;
    protected $created_at;
    
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
    
    public function getId() {
        return $this->id;
    }
    
    public function getCreatedAt() {
        return $this->created_at;
    }
    
    protected function setId($id): void {
        $this->id = $id;
    }
    
    protected function setCreatedAt($created_at): void {
        $this->created_at = $created_at;
    }
}
