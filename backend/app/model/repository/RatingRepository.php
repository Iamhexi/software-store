<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../Rating.php';

class RatingRepository implements Repository {
    
    private Database $database;
    private const CLASS_NAME = 'Rating';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    function find(int $id): ?Rating {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE rating_id = :rating_id;",
            params: ['rating_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }
    
    public function fina_all(): array {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }
    
    function save(Rating $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:review_id, :author_id, :software_id, :mark, :date_added)",
            params: [
                'rating_id' => $object->rating_id?? NULL,
                'author_id' => $object->author_id,
                'software_id' => $object->software_id,
                'mark' => $object->mark,
                'date_added' => $object->date_added
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE rating_id = :rating_id;",
            params: ['rating_id' => $id]
        );
    }
}