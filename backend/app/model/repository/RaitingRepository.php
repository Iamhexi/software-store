<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../Raiting.php';

class RaitingRepository implements Repository {
    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'Raiting';
    function find(int $id): ?Raiting {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE raiting_id = :raiting_id;",
            params: ['raiting_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }
    
    function findAll(): array {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }
    
    function save(Raiting $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:review_id, :author_id, :software_id, :mark, :date_added)",
            params: [
                'raiting_id' => $object->raiting_id?? "NULL",
                'author_id' => $object->author_id,
                'software_id' => $object->software_id,
                'mark' => $object->mark,
                'date_added' => $object->date_added
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE raiting_id = :raiting_id;",
            params: ['raiting_id' => $id]
        );
    }
}