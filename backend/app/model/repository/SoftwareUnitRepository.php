<?php
require_once __DIR__ . '/../SoftwareUnit.php';
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/CategoryRepository.php';

class SoftwareUnitRepository implements Repository {
    private Database $database;
    private CategoryRepository $category_repository;

    private const CLASS_NAME = 'SoftwareUnit';


    public function __construct(Database $database = new PDODatabase,
                                CategoryRepository $category_repository = new CategoryRepository()) {
        $this->database = $database;
        $this->category_repository = $category_repository;
    }

    public function find(int $id): ?SoftwareUnit {
        $row = $this->database->get_rows(
            query: "SELECT * FROM SoftwareUnit WHERE software_id = :software_id;",
            params: ['software_id' => $id],
            class_name: 'SoftwareUnit',
            number: 1
        );

        if ($row === null)
            return null;

        $categories = $this->category_repository->find_all_categories_for_software($id);
      
        return new SoftwareUnit(
            software_id: $row->software_id,
            author_id: $row->author_id,
            name: $row->name,
            description: $row->description,
            link_to_graphic: $row->link_to_graphic,
            is_blocked: $row->is_blocked,
            categories: $categories
        );
    }

    public function find_all(): array {
        $created_class = self::CLASS_NAME;
        $rows =  $this->database->get_rows(
            query: "SELECT * FROM $created_class",
            class_name: 'stdClass'
        );

        foreach ($rows as $row){
            $categories = $this->category_repository->find_all_categories_for_software($row->software_id);
            $software_units[] = new SoftwareUnit(
                software_id: $row->software_id,
                author_id: $row->author_id,
                name: $row->name,
                description: $row->description,
                link_to_graphic: $row->link_to_graphic,
                is_blocked: $row->is_blocked,
                categories: $categories
            );
        }
        return $software_units ?? [];
    }
    
    public function find_by(string $column, mixed $value): ?object {
        $row = $this->database->get_rows(
            query: "SELECT * FROM SoftwareUnit WHERE $column = :$column;",
            params: [$column => $value],
            class_name: 'SoftwareUnit',
            number: 1
        );

        if ($row === null)
            return null;

        $categories = $this->category_repository->find_all_categories_for_software($row->software_id);

        return new SoftwareUnit(
            software_id: $row->software_id,
            author_id: $row->author_id,
            name: $row->name,
            description: $row->description,
            link_to_graphic: $row->link_to_graphic,
            is_blocked:  intval($row->is_blocked) === 1 ? true : false,
            categories: $categories
        );
    }

    public function save(object $object): bool {

        if ($object->software_id !== null)
            return $this->update($object);

        return $this->database->execute_query(
            query: "INSERT INTO SoftwareUnit VALUES (:software_id, :author_id, :name, :description, :link_to_graphic, :is_blocked)",
            params: [
                'software_id' => $object->software_id,
                'author_id' => $object->author_id,
                'name' => $object->name,
                'description' => $object->description,
                'link_to_graphic' => $object->link_to_graphic,
                'is_blocked' => $object->is_blocked ? 1 : 0
            ]
        );
    }

    public function update(object $object): bool {
        return $this->database->execute_query(
            query: "UPDATE SoftwareUnit SET author_id = :author_id, name = :name, description = :description, link_to_graphic = :link_to_graphic, is_blocked = :is_blocked WHERE software_id = :software_id;",
            params: [
                'software_id' => $object->software_id,
                'author_id' => $object->author_id,
                'name' => $object->name,
                'description' => $object->description,
                'link_to_graphic' => $object->link_to_graphic,
                'is_blocked' => $object->is_blocked ? 1 : 0
            ]
        );
    }

    
    public function delete(int $id): bool {

        return $this->database->execute_query(
            query: "CALL PurgeSoftware(:software_id);",
            params: ['software_id' => $id]
        );
    }
}