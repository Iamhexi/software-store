<?php
require_once __DIR__ .'/Repository.php';
require_once __DIR__ .'/../Category.php';
require_once __DIR__.'/../PDODatabase.php';

class CategoryRepository implements Repository {

    private Database $database;
    private const CLASS_NAME = 'Category';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }
    
    function find(int $id): ?Category {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE category_id = :category_id;",
            params: ['category_id' => $id],
            class_name: $created_class,
            number: 1
        );

        if ($row === null)
            return null;


        return new Category(
            category_id: $id,
            name: $row->name,
            description: $row->description
        );
    }

    public function find_all(): array {
        $created_class = 'stdClass';
        $rows = $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
        );

        foreach ($rows as $row) {
            $categories[] = new Category(
                category_id: $row->category_id,
                name: $row->name,
                description: $row->description
            );
        }
        return $categories ?? [];
    }

    public function find_by(string $column, mixed $value): ?Category {
        $created_class = self::CLASS_NAME;
        if (!property_exists($created_class, $column)) {
            Logger::log("Column $column does not exist in table $created_class", Priority::ERROR);
            return null;
        }

        if ($column === 'category_id')
            return $this->find($value);

        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE $column = :value;",
            params: ['value' => $value],
            class_name: $created_class,
            number: 1
        );

        if ($row === null)
            return null;

        return new Category(
            category_id: $row->category_id,
            name: $row->name,
            description: $row->description
        );
    }

    public function save(object $object): bool {
        $created_class = self::CLASS_NAME;

        if ($object->category_id !== null && $this->find($object->category_id) !== null)
            return $this->database->execute_query(
                query: "UPDATE $created_class SET name = :name, description = :description WHERE category_id = :category_id;",
                params: [
                    'category_id' => $object->category_id,
                    'name' => $object->name,
                    'description' => $object->description
                ]
            );
    }
    
    public function delete(int $id): bool {
        $class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "DELETE FROM $class WHERE category_id = :category_id;",
            params: ['category_id' => $id]
        );
    }
}