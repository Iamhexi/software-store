<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../SourceCode.php';

class SourceCodeRepository {
    private Database $database;

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    function find(int $id): ?SourceCode {
        return $this->database->get_rows(
            query: "SELECT * FROM SourceCode WHERE code_id = :code_id;",
            params: ['code_id' => $id],
            class_name: 'SourceCode',
            number: 1
        );
    }

    function find_all(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM SourceCode;",
            class_name: 'SourceCode'
        );
    }

    function save(SourceCode $object): bool {
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