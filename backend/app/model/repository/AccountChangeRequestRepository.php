<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../AccountChangeRequest.php';

class AccountChangeRequestRepository {

    private Database $database;

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    function find(int $id): ?object {
        return $this->database->get_rows(
            query: "SELECT * FROM AccountChangeRequest WHERE request_id = :request_id;",
            params: ['request_id' => $id],
            class_name: 'AccountChangeRequest',
            number: 1
        );
    }

    function find_by(string $column, string|int $value): ?object {
        $object = $this->database->get_rows(
            query: "SELECT * FROM AccountChangeRequest WHERE $column = :$column;",
            params: [$column => $value],
            class_name: 'AccountChangeRequest',
            number: 1
        );

        if ($object === null)
            return null;

        return new AccountChangeRequest(
            request_id: $object->request_id,
            user_id: $object->user_id,
            description: $object->description,
            justification: $object->justification,
            date_submitted: new DateTime($object->date_submitted),
            review_status: RequestStatus::from($object->review_status)
        );
    }

    function find_all(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM AccountChangeRequest;",
            class_name: 'AccountChangeRequest'
        );
    }

    function save(AccountChangeRequest $object): bool {
        return $this->database->execute_query(
            query: "INSERT INTO AccountChangeRequest VALUES (:request_id, :user_id, :description, :justification, :date_submitted, :review_status)",
            params: [
                'request_id' => $object->request_id ?? NULL,
                'user_id' => $object->user_id,
                'description' => $object->description,
                'justification' => $object->justification,
                'date_submitted' => $object->date_submitted->format('Y-m-d'),
                'review_status' => $object->review_status->value
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