<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../Download.php';

class DownloadRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'Download';
    
    function find(int $id): ?Download {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE download_id = :download_id;",
            params: ['download_id' => $id],
            class_name: $created_class,
            number: 1
        );
    }
    
    function findAll(): array {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
        );
    }
    
    function save(Download $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:download_id, :user_id, :executable_id, 
            :date_download)",
            params: [
                'download_id' => $object->download_id?? "NULL",
                'user_id' => $object->user_id,
                'executable_id' => $object->executable_id,
                'date_download' => $object->date_download
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