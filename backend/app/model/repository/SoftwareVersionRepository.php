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

        $row = null;

        if ($column === 'version_id')
            return $this->find($value);
        else if ($column === 'software_id')
            $row = $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE software_id = :software_id;",
                params: ['software_id' => $value],
                number: 1
            );
        else if ($column === 'version') {
            $major_version = $value->major_version;
            $minor_version = $value->minor_version;
            $patch_version = $value->patch_version;
            $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE major_version = :major_version AND minor_version = :minor_version AND patch_version = :patch_version;",
                params: [
                    'major_version' => $major_version,
                    'minor_version' => $minor_version,
                    'patch_version' => $patch_version
                ],
                number: 1
            );
            
        }
        else if ($column === 'date_added')
            $row = $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE date_added = :date_added;",
                params: ['date_added' => $value],
                number: 1
            );
        else if ($column === 'description')
            $row = $this->database->get_rows(
                query: "SELECT * FROM $created_class WHERE description = :description;",
                params: ['description' => $value],
                number: 1
            );
        else
            return null;

        if ($row === null)
            return null;
        
        return new SoftwareVersion(
            version_id: $row->version_id,
            software_id: $row->software_id,
            description: $row->description,
            date_added: new DateTime($row->date_added),
            version: new Version(
                major: $row->major_version,
                minor: $row->minor_version,
                patch: $row->patch_version
            )
        );
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
                'date_added' => $object->date_added,
                'major_version' => $object->version->major,
                'minor_version' => $object->version->minor,
                'patch_version' => $object->version->patch
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