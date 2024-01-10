<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../AccountChangeRequest.php';

class AccountChangeRequestRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'AccountChangeRequest';
    
    function find(int $id): ?AccountChangeRequest {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE request_id = :request_id;",
            params: ['request_id' => $id],
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
    
    function save(AccountChangeRequest $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:request_id, :user_id, :description, :justification, 
            :date_submitted, :review_status)",
            params: [
                'request_id' => $object->request_id ?? "NULL",
                'user_id' => $object->user_id,
                'description' => $object->description,
                'justification' => $object->justification,
                'date_submitted' => $object->date_submitted,
                'review_status' => $object->review_status
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE request_id = :request_id;",
            params: ['request_id' => $id]
        );
    }
}