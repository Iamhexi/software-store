<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../Review.php';

class ReviewRepository implements Repository {
    private Database $db = new PDODatabase;
    private const CLASS_NAME = 'Review';
    function find(int $id): ?Review {
        $created_class = self::CLASS_NAME;
        return $this->db->get_rows(
            query: "SELECT * FROM $created_class WHERE review_id = :review_id;",
            params: ['review_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }
    
    function findAll(): array {
        $created_class = self::CLASS_NAME;
        return $this->db->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }
    
    function save(Review $object): bool {
        // TODO: implement
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->db->execute_query(
            query: "DELETE $class WHERE review_id = :review_id;",
            params: ['review_id' => $id]
        );
    }
}