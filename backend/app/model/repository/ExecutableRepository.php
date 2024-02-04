<?php
require_once __DIR__ . '/Repository.php';

class ExecutableRepository implements Repository {
    private Database $database;
    private const CLASS_NAME = 'Executable';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find(int $id): ?object {

        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE executable_id = :executable_id;",
            params: ['executable_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }
  
    public function find_all(): array {

        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }

    function find_by(array $conditions): array {
        $class_name = self::CLASS_NAME;
        $allowed_columns = ['executable_id', 'version_id', 'target_architecture', 'date_compiled', 'filepath'];
        
        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowed_columns))
                throw new InvalidArgumentException("Column '$column' is not allowed as a condition in $class_name::find_by(...)");
            else if ($column === 'request_id')
                return [$this->find($value)];
        }

        // build query
        $query = "SELECT * FROM $class_name WHERE ";
        foreach($conditions as $column => $value)
            $query .= " $column = :$column AND";
        
        $query = substr($query, 0, -3) . ';'; // remove the last AND

        $rows = $this->database->get_rows(
            query: $query,
            params: $conditions
        );


        foreach ($rows as $row) {

            $objects[] = new $class_name(
                executable_id: $row->executable_id,
                version_id: $row->version_id,
                target_architecture: Architecture::from($row->target_architecture),
                date_compiled: new DateTime($row->date_compiled),
                filepath: $row->filepath
            );
        }

        return $objects ?? [];
    }


    public function save(object $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:executable_id, :version_id, :target_architecture, :date_compiled, :filepath)",
            params: [
                'executable_id' => $object->executable_id ?? NULL,
                'version_id' => $object->version_id,
                'target_architecture' => $object->target_architecture,
                'date_compiled' => $object->date_compiled,
                'filepath' => $object->filepath
            ]
        );
    }

    public function delete(int $id): bool {

        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE executable_id = :executable_id;",
            params: ['executable_id' => $id]
        );
    }
}