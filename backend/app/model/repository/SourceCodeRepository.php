<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../SourceCode.php';

class SourceCodeRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'SourceCode';
    
    function find(int $id): ?SoftwareUnit {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE code_id = :code_id;",
            params: ['code_id' => $id],
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
    
    function save(SourceCode $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:code_id, :version_id, :filepath)",
            params: [
                'code_id' => $object->code_id ?? "NULL",
                'version_id' => $object->version_id,
                'filepath' => $object->filepath
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE code_id = :code_id;",
            params: ['code_id' => $id]
        );
    }
}