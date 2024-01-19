<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../Review.php';

class ReviewRepository implements Repository {
    
    private Database $database;
    private const CLASS_NAME = 'Review';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    function find(int $id): ?object {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE review_id = :review_id;",
            params: ['review_id' => $id],
            class_name: 'stdClass',
            number: 1
        );  

        if ($row === null)
            return null;

        return new Review(
            review_id: $row->review_id,
            author_id: $row->author_id,
            software_id: $row->software_id,
            title: $row->title,
            description: $row->description,
            date_added: new DateTime($row->date_added),
            date_last_updated: new DateTime($row->date_last_updated)
        );
    }
    
    function find_all(): array {
        $created_class = self::CLASS_NAME;
        $rows = $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: 'stdClass'
        );

        foreach ($rows as $row) {
            $reviews[] = new Review(
                review_id: $row->review_id,
                author_id: $row->author_id,
                software_id: $row->software_id,
                title: $row->title,
                description: $row->description,
                date_added: new DateTime($row->date_added),
                date_last_updated: new DateTime($row->date_last_updated)
            );
        }

        return $reviews ?? [];
    }
    
    function save(object $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:review_id, :author_id, :software_id, :title, :description, :date_added, :date_last_updated)",
            params: [
                'review_id' => $object->review_id?? NULL,
                'author_id' => $object->author_id,
                'software_id' => $object->software_id,
                'title' => $object->title,
                'description' => $object->description,
                'date_added' => $object->date_added ?? date("Y-m-d"),
                'date_last_updated' => $object->date_last_updated ?? date("Y-m-d")
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE review_id = :review_id;",
            params: ['review_id' => $id]
        );
    }

    public function find_by(string $column, $value): ?object {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE $column = :value;",
            params: ['value' => $value],
            class_name: 'stdClass',
            number: 1
        );
    

        if ($row === null)
            return null;

            
        return new Review(
            review_id: $row->review_id,
            author_id: $row->author_id,
            software_id: $row->software_id,
            title: $row->title,
            description: $row->description,
            date_added: new DateTime($row->date_added),
            date_last_updated: new DateTime($row->date_last_updated)
        );
    }   
}