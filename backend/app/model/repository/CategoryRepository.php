<?php
require_once __DIR__ .'/Repository.php';
require_once __DIR__ .'/../Category.php';

class CategoryRepository {
    private Database $database;
    private const CLASS_NAME = 'Category';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find(int $id): ?Category {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE category_id = :category_id;",
            params: ['category_id' => $id],
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

    public function find_by(string $column, mixed $value): ?Category {
        $created_class = self::CLASS_NAME;
        if (!property_exists($created_class, $column)) {
            Logger::log("Column $column does not exist in table $created_class", Priority::ERROR);
            return null;
        }

        if ($column === 'category_id')
            return $this->find($value);

        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE $column = :value;",
            params: ['value' => $value],
            class_name: $created_class,
            number: 1
        );
    }

    public function save(object $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:category_id, :name, :description)",
            params: [
                'category_id' => $object->category_id ?? NULL,
                'name' => $object->name,
                'description' => $object->description
            ]
        );
    }

    public function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE category_id = :category_id;",
            params: ['category_id' => $id]
        );
    }
}