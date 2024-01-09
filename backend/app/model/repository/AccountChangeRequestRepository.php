<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../AccountChangeRequest.php';

class AccountChangeRequestRepository {

    private Database $database;

    public function __construct() {
        $this->database = new PDODatabase;
    }

    function find(int $id): ?AccountChangeRequest {
        return $this->database->get_rows(
            query: "SELECT * FROM AccountChangeRequest WHERE request_id = :request_id;",
            params: ['request_id' => $id],
            class_name: 'AccountChangeRequest',
            number: 1
        );
    }

    function findAll(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM AccountChangeRequest;",
            class_name: 'AccountChangeRequest'
        );
    }

    function save(AccountChangeRequest $object): bool {
        return $this->database->execute_query(
            query: "INSERT INTO AccountChangeRequest VALUES (:request_id, :user_id, :description, :date_submitted, :review_status)",
            params: [
                'request_id' => $object->request_id?? "NULL",
                'user_id' => $object->user_id,
                'description' => $object->description,
                'date_submitted' => $object->date_submitted->format('Y-m-d H:i:s'),
                'review_status' => $object->review_status
            ]
        );
    }

    function delete(int $id): bool {
        return $this->database->execute_query(
            query: "DELETE AccountChangeRequest WHERE request_id = :request_id;",
            params: ['request_id' => $id]
        );
    }

}