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
        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE version_id = :version_id;",
            params: ['version_id' => $id],
            number: 1
        );

        if ($row === null)
            return null;

        return new $created_class(
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
  
    function find_by(array $conditions): array {
        $class_name = self::CLASS_NAME;
        $allowed_columns = ['version_id', 'software_id', 'description', 'date_added', 'major_version', 'minor_version', 'patch_version'];

        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowed_columns))
                throw new InvalidArgumentException("Column '$column' is not allowed as a condition in $class_name::find_by(...)");
            else if ($column === 'version_id')
                return [$this->find($value)];
        }

        // build query
        $query = "SELECT * FROM $class_name WHERE ";
        foreach($conditions as $column => $value)
            $query .= "$column = :$column AND";
        
        $query = substr($query, 0, -3) . ';'; // remove the last AND

        $rows = $this->database->get_rows(
            query: $query,
            params: $conditions
        );

        foreach ($rows as $row) {

            $objects[] = new $class_name(
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

        return $objects ?? [];
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