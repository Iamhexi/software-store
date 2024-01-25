<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../SourceCode.php';

class SourceCodeRepository implements Repository {
    private Database $database;

    private const CLASS_NAME = 'SourceCode';
    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    function find(int $id): ?object {
        $row = $this->database->get_rows(
            query: "SELECT * FROM SourceCode WHERE code_id = :code_id;",
            params: ['code_id' => $id],
            number: 1
        );

        if ($row === null)
            return null;

        return new SourceCode(
            code_id: $row->code_id,
            version_id: $row->version_id,
            filepath: $row->filepath
        );
    }

    function find_all(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM SourceCode;",
            class_name: 'SourceCode'
        );
    }

    function find_by(array $conditions): array {
        $class_name = self::CLASS_NAME;
        $allowed_columns = ['code_id', 'version_id', 'filepath'];

        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowed_columns))
                throw new InvalidArgumentException("Column '$column' is not allowed as a condition in $class_name::find_by(...)");
            else if ($column === 'code_id')
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
                code_id: $row->code_id,
                version_id: $row->version_id,
                filepath: $row->filepath
            );
        }

        return $objects ?? [];
    }

    function save(object $object): bool {
        return $this->database->execute_query(
            query: "INSERT INTO SourceCode VALUES (:code_id, :version_id, :filepath)",
            params: [
                'code_id' => $object->code_id,
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