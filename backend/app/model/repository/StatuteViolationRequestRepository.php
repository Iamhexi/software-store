<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../StatuteViolationRequest.php';

class StatuteViolationRequestRepository {
    private Database $database;

    public function __construct() {
        $this->database = new PDODatabase;
    }

    function find(int $id): ?StatuteViolationRequest {
        return $this->database->get_rows(
            query: "SELECT * FROM StatuteViolationRequest WHERE report_id = :report_id;",
            params: ['report_id' => $id],
            class_name: 'StatuteViolationRequest',
            number: 1
        );
    }

    function findAll(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM StatuteViolationRequest;",
            class_name: 'StatuteViolationRequest'
        );
    }

    function save(StatuteViolationRequest $object): bool {
        return $this->database->execute_query(
            query: "INSERT INTO StatuteViolationRequest VALUES (:report_id, :software_id, :description, :rule_point, :date_added, :review_status)",
            params: [
                'report_id' => $object->report_id ?? "NULL",
                'software_id' => $object->software_id,
                'description' => $object->description,
                'rule_point' => $object->rule_point,
                'date_added' => $object->date_added->format('Y-m-d H:i:s'),
                'review_status' => $object->review_status
            ]
        );
    }

    function delete(int $id): bool {
        return $this->database->execute_query(
            query: "DELETE StatuteViolationRequest WHERE report_id = :report_id;",
            params: ['report_id' => $id]
        );
    }
}