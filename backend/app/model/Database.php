<?php 

interface Database {
    public function execute_query(string $query, array $params = []): bool;
}