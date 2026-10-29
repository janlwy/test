<?php

namespace Models;

abstract class AbstractModel
{
    protected ?int $id = null;
    protected ?string $created_at = null;
    protected ?int $user_id = null;

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }

    public function hydrate(array $data): void
    {
        foreach ($data as $key => $value) {
            // Convertit snake_case (ex: created_at, user_id) en CamelCase (setCreatedAt, setUserId)
            $camelKey = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));
            $method = 'set' . $camelKey;
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setId($id): void
    {
        $this->id = $id !== null ? (int)$id : null;
    }

    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUserId($user_id): void
    {
        $this->user_id = $user_id !== null ? (int)$user_id : null;
    }

    public function toArray(): array
    {
        $reflect = new \ReflectionClass($this);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

        $array = [];
        foreach ($props as $prop) {
            $prop->setAccessible(true);
            $val = $prop->getValue($this);
            if ($val !== null) {
                $array[$prop->getName()] = $val;
            }
        }
        return $array;
    }

    public function validate(): array
    {
        return [];
    }

    public function isValid(): bool
    {
        return empty($this->validate());
    }
}
