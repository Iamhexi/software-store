<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../SoftwareUnit.php';

class SoftwareUnitRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'SoftwareUnit';
    
    function find(int $id): ?SoftwareUnit {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE software_id = :software_id;",
            params: ['software_id' => $id],
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
    
    function save(SoftwareUnit $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:software_id, :author_id, :name, :description, 
            :link_to_graphic, :is_blocked)",
            params: [
                'software_id' => $object->software_id ?? "NULL",
                'author_id' => $object->author_id,
                'name' => $object->name,
                'description' => $object->description,
                'link_to_graphic' => $object->link_to_graphic,
                'is_blocked' => $object->is_blocked
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE software_id = :software_id;",
            params: ['software_id' => $id]
        );
    }
}