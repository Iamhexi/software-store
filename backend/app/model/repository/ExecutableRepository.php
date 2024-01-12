<?php
require_once __DIR__ . '/Repository.php';

class ExecutableRepository implements Repository {
    private Database $database;
    private const CLASS_NAME = 'Executable';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find(int $id): ?Executable {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE executable_id = :executable_id;",
            params: ['executable_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }

    public function findAll(): array {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
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