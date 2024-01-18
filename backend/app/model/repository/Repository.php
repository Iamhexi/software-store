<?php

interface Repository {
    public function find(int $id): ?object;
    public function find_all(): array;
    public function find_by(string $column, mixed $value): ?object;
    public function save(object $object): bool;
    public function delete(int $id): bool;
}