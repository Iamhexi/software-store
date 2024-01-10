<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../Executable.php';

class ExecutableRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'Executable';
    
    function find(int $id): ?Executable {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE executable_id = :executable_id;",
            params: ['executable_id' => $id],
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
    
    function save(Executable $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:executable_id, :version_id, :target_architecture, 
            :date_compiled, :filepath)",
            params: [
                'executable_id' => $object->executable_id?? "NULL",
                'version_id' => $object->version_id,
                'target_architecture' => $object->target_architecture,
                'date_compiled' => $object->date_compiled,
                'filepath' => $object->filepath
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE executable_id = :executable_id;",
            params: ['executable_id' => $id]
        );
    }
}