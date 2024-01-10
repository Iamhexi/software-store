<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../StatuteViolationReport.php';

class StatuteViolationReportRepository implements Repository {

    private Database $database = new PDODatabase;
    private const CLASS_NAME = 'StatuteViolationReport';
    
    function find(int $id): ?StatuteViolationReport {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE report_id = :report_id;",
            params: ['report_id' => $id],
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
    
    function save(StatuteViolationReport $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:report_id, :software_id, :user_id, :rule_point, 
                    :description, :date_added, :review_status)",
            params: [
                'report_id' => $object->report_id?? "NULL",
                'software_id' => $object->software_id,
                'user_id' => $object->user_id,
                'rule_point' => $object->rule_point,
                'description' => $object->description,
                'date_added' => $object->date_added,
                'review_status' => $object->review_status
            ]
        );
    }
    
    function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE report_id = :report_id;",
            params: ['report_id' => $id]
        );
    }
}