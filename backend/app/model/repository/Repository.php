<?php

interface Repository {
    public function find(int $id): ?object;
    public function find_all(): array;
    public function find_by(array $conditions): array;
    public function save(object $object): bool;
    public function delete(int $id): bool;
}