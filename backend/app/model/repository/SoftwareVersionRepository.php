<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../SoftwareVersion.php';

class SoftwareVersionRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'SoftwareVersion';
    
    function find(int $id): ?SoftwareUnit {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE version_id = :version_id;",
            params: ['version_id' => $id],
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
    
    function save(SoftwareVersion $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:version_id, :software_id, :description, :date_added, 
            :major_version, :minor_version, :patch_version)",
            params: [
                'version_id' => $object->version_id ?? "NULL",
                'software_id' => $object->software_id,
                'description' => $object->description,
                'date_added' => $object->date_added,
                'major_version' => $object->major_version,
                'minor_version' => $object->minor_version,
                'patch_version' => $object->patch_version
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE version_id = :version_id;",
            params: ['version_id' => $id]
        );
    }
}