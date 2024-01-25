<?php
require_once __DIR__ . '/Repostory.php';
require_once __DIR__ . '/../Download.php';
require_once __DIR__.'/../PDODatabase.php';
              
class DownloadRepository implements Repository {
    
    private Database $database;
    private const CLASS_NAME = 'Download';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find(int $id): ?Download {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE download_id = :download_id;",
            params: ['download_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }

    function find_by(array $conditions): array {
        $class_name = self::CLASS_NAME;
        $allowed_columns = array_keys(get_class_vars($class_name));

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
                download_id: $row->download_id,
                user_id: $row->user_id,
                executable_id: $row->executable_id,
                download_date: new DateTime($row->download_date)
            );
        }

        return $objects ?? [];
    }

  
    public function find_all(): array {

        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }

    public function save(Download $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:download_id, :user_id, :executable_id, :download_date)",
            params: [
                'download_id' => $object->download_id?? NULL,
                'user_id' => $object->user_id,
                'executable_id' => $object->executable_id,
                'download_date' => $object->download_date
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE download_id = :download_id;",
            params: ['download_id' => $id]
        );
    }
}