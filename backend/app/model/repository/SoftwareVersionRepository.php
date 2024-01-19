<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../SoftwareVersion.php';

class SoftwareVersionRepository {

    private Database $database;

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }
    
    public function find(int $id): ?object {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE version_id = :version_id;",
            params: ['version_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }
  
    public function find_by(string $column, mixed $value): ?object {
        // TODO: implement this 
    }
    
    public function findAll(): array {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }
    
   public function save(SoftwareVersion $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:version_id, :software_id, :description, :date_added, 
            :major_version, :minor_version, :patch_version)",
            params: [
                'version_id' => $object->version_id?? NULL,
                'software_id' => $object->software_id,
                'description' => $object->description,
                'date_added' => $object->date_added->format('Y-m-d'),
                'major_version' => $object->major_version,
                'minor_version' => $object->minor_version,
                'patch_version' => $object->patch_version
            ]
        );
    }
    
    public function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE version_id = :version_id;",
            params: ['version_id' => $id]
        );
    }
}