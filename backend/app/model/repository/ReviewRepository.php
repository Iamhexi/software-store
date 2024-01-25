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
    

     public function save(object $object): bool {
        if ($this->in_database($object))
            return $this->update($object);
        return $this->insert($object);
    }

    private function insert(object $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:review_id, :author_id, :software_id, :title, :description, :date_added, :date_last_updated)",
            params: [
                'review_id' => $object->review_id,
                'author_id' => $object->author_id,
                'software_id' => $object->software_id,
                'title' => $object->title,
                'description' => $object->description,
                'date_added' => $object->date_added,
                'date_last_updated' => $object->date_last_updated
            ]
        );
    }

    private function in_database(Review $review): bool {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE review_id = :review_id;",
            params: ['review_id' => $review->review_id],
            class_name: 'stdClass',
            number: 1
        );  

        return $row !== null;
    }
    
    private function update(Review $review): bool {
        $table = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "UPDATE $table SET title = :title, description = :description, date_last_updated = :date_last_updated WHERE review_id = :review_id",
            params: [
                'review_id' => $review->review_id?? NULL,
                'title' => $review->title,
                'description' => $review->description,
                'date_last_updated' => $review->date_last_updated ?? date("Y-m-d")
            ]
        );
    }

    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE FROM $class WHERE review_id = :review_id;",
            params: ['review_id' => $id]
        );
    }

    function find_by(array $conditions): array {
        $class_name = self::CLASS_NAME;
        $allowed_columns = ['review_id', 'author_id', 'software_id', 'title', 'description', 'date_added', 'date_last_updated'];

        foreach ($conditions as $column => $value) {
            if (!in_array($column, $allowed_columns))
                throw new InvalidArgumentException("Column '$column' is not allowed as a condition in $class_name::find_by(...)");
            else if ($column === 'request_id')
                return [$this->find($value)];
        }

        // build query
        $query = "SELECT * FROM $class_name WHERE ";
        foreach($conditions as $column => $value)
            $query .= " $column = :$column AND";
        
        $query = substr($query, 0, -3) . ';'; // remove the last AND

        $rows = $this->database->get_rows(
            query: $query,
            params: $conditions
        );



        foreach ($rows as $row) {

            $objects[] = new $class_name(
                review_id: $row->review_id,
                author_id: $row->author_id,
                software_id: $row->software_id,
                title: $row->title,
                description: $row->description,
                date_added: new DateTime($row->date_added),
                date_last_updated: new DateTime($row->date_last_updated)
            );
        }

        return $objects ?? [];
    }
}