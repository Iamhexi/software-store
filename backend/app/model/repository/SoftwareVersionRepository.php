<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../SoftwareVersion.php';

class SoftwareVersionRepository implements Repository {

    private Database $database;
    private const CLASS_NAME = 'SoftwareVersion';

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
        $created_class = self::CLASS_NAME;
        if (!property_exists($created_class, $column)) {
            Logger::log("Column $column does not exist in table $created_class", Priority::ERROR);
            return null;
        }

        if ($column === 'version_id')
            return $this->find($value);
        else if ($column === 'software_id')
            return $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE software_id = :software_id;",
                params: ['software_id' => $value],
                class_name: $created_class,
                number: 1
            );
        else if ($column === 'major_version')
            return $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE major_version = :major_version;",
                params: ['major_version' => $value],
                class_name: $created_class,
                number: 1
            );
        else if ($column === 'minor_version')
            return $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE minor_version = :minor_version;",
                params: ['minor_version' => $value],
                class_name: $created_class,
                number: 1
            );
        else if ($column === 'patch_version')
            return $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE patch_version = :patch_version;",
                params: ['patch_version' => $value],
                class_name: $created_class,
                number: 1
            );
        else if ($column === 'date_added')
            return $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE date_added = :date_added;",
                params: ['date_added' => $value],
                class_name: $created_class,
                number: 1
            );
        else if ($column === 'description')
            return $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE description = :description;",
                params: ['description' => $value],
                class_name: $created_class,
                number: 1
            );
        else
            return null;
    }
    
    public function find_all(): array {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }
    
   public function save(object $object): bool {
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