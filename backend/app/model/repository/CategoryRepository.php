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
        $table = self::CLASS_NAME;
        $rows = $this->database->get_rows(
            query: "SELECT * FROM $table",
            class_name: 'stdClass'
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

    public function find_all_categories_for_software(int $software_id): array {
        $table = 'SoftwareCategory';
        $rows = $this->database->get_rows(
            query: "SELECT category_id FROM $table WHERE software_id = $software_id",
            class_name: 'stdClass'
        );

        if ($rows === null)
            return [null];

        foreach ($rows as $row) {
            $categories[] = $this->find($row->category_id);
        }

        return $categories ?? [];
    }

    public function find_by(array $conditions): array {
        $class_name = self::CLASS_NAME;
        $allowed_columns = ['category_id', 'name', 'description'];

        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowed_columns))
                throw new InvalidArgumentException("Column '$column' is not allowed as a condition in $class_name::find_by(...)");
            else if ($column === 'request_id')
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
                category_id: $row->category_id,
                name: $row->name,
                description: $row->description
            );
        }

        return $objects ?? [];
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
        else return false;
    }
    
    public function delete(int $id): bool {
        $class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "DELETE FROM $class WHERE category_id = :category_id;",
            params: ['category_id' => $id]
        );
    }
}