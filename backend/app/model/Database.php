<?php 

interface Database {
    public function execute_query(string $query, array $params = []): bool;
    public function get_rows(
        string $query,
        array $params = [],
        string $class_name = 'stdClass',
        int $number = PHP_INT_MAX
    ): array|object|null;
}