<?php

interface IRepository {
    public function findById(int $id);
    public function findAll(): array;
    public function findAllByUser(int $userId): array;
    public function save($entity): void;
    public function delete(int $id): void;
}
