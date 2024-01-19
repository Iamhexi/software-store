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

    public function get_average(int $software_id): float {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT AVG(mark) 'average' FROM $created_class WHERE software_id = :software_id;",
            params: ['software_id' => $software_id],
        );

        if ($row === null || $row->average === null)
            return 0.0;

        return floatval($row->average);
    }

    public function get_count(int $software_id): int {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT COUNT(*) 'count' FROM $created_class WHERE software_id = :software_id;",
            params: ['software_id' => $software_id],
        );

        if ($row === null || $row->count === null)
            return 0;


        return intval($row->count);
    }

    public function find_by(string $column, mixed $value): ?object {
        $created_class = self::CLASS_NAME;

        if (!property_exists($created_class, $column)) {
            Logger::log("Column $column does not exist in table $created_class", Priority::ERROR);
            return null;
        }

        if ($column === 'rating_id')
            return $this->find($value);

        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE $column = :value",
            params: ['value' => $value],
            class_name: $created_class,
            number: 1
        );

        if ($row === null)
            return null;

        return new Rating(
            rating_id: $row->rating_id,
            author_id: $row->author_id,
            software_id: $row->software_id,
            mark: $row->mark,
            date_added: new DateTime($row->date_added)
        );
    }

    public function find(int $id): ?Rating {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE rating_id = :rating_id;",
            params: ['rating_id' => $id],
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
    
    public function save(object $object): bool {
        $created_class = self::CLASS_NAME;
        if (!($object instanceof Rating)) {
            Logger::log("Object passed to save() method in RatingRepository is not of type $created_class", Priority::ERROR);
            return false;
        }

        if ($object->rating_id !== null || $this->find_by('author_id', $object->author_id) !== null) {
            Logger::log('Attempt to insert a duplicate rating', Priority::INFO);
            return false;
        }

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:rating_id, :author_id, :software_id, :mark, :date_added)",
            params: [
                'rating_id' => $object->rating_id,
                'author_id' => $object->author_id,
                'software_id' => $object->software_id,
                'mark' => $object->mark,
                'date_added' => $object->date_added
            ]
        );
    }
    
    public function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE rating_id = :rating_id;",
            params: ['rating_id' => $id]
        );
    }
}