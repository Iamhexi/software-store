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