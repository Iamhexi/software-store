<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../Category.php';

class CategoryRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'Category';
    
    function find(int $id): ?Category {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE category_id = :category_id;",
            params: ['category_id' => $id],
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
    
    function save(Category $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:category_id, :name, :description)",
            params: [
                'category_id' => $object->category_id?? "NULL",
                'name' => $object->name,
                'description' => $object->description
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE category_id = :category_id;",
            params: ['category_id' => $id]
        );
    }
}