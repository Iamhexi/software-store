<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../StatuteViolationReport.php';

class StatuteViolationReportRepository implements Repository {

    private Database $database;
    private const CLASS_NAME = 'StatuteViolationReport';
    
    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }
    public function find(int $id): ?StatuteViolationReport {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE report_id = :report_id;",
            params: ['report_id' => $id],
            class_name: 'stdClass',
            number: 1
        );

        if ($row === null)
            return null;

        return new StatuteViolationReport(
            report_id: $row->report_id,
            software_id: $row->software_id,
            user_id: $row->user_id,
            rule_point: $row->rule_point,
            description: $row->description,
            date_added: new Datetime($row->date_added),
            review_status: RequestStatus::convert_string_to_request_status($row->review_status)
        );
    }
    
    public function find_All(): array {
        $created_class = self::CLASS_NAME;
        $rows = $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: 'stdClass'
        );

        foreach($rows as $row) {
            $reports[] = new StatuteViolationReport(
                report_id: $row->report_id,
                software_id: $row->software_id,
                user_id: $row->user_id,
                rule_point: $row->rule_point,
                description: $row->description,
                date_added: new Datetime($row->date_added),
                review_status: RequestStatus::convert_string_to_request_status($row->review_status)
            );
        }

        return $reports ?? [];
    }

    public function find_All_by_id($software_id): array {
        $created_class = self::CLASS_NAME;
        $rows = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE software_id = :software_id;",
            class_name: 'stdClass',
            params: ['software_id' => $software_id]
        );

        foreach($rows as $row) {
            $reports[] = new StatuteViolationReport(
                report_id: $row->report_id,
                software_id: $row->software_id,
                user_id: $row->user_id,
                rule_point: $row->rule_point,
                description: $row->description,
                date_added: new Datetime($row->date_added),
                review_status: RequestStatus::convert_string_to_request_status($row->review_status)
            );
        }

        return $reports ?? [];
    }
    public function find_by(string $column, mixed $value): ?object {
        $row = $this->database->get_rows(
            query: "SELECT * FROM StatuteViolationReport WHERE $column = :$column;",
            params: [$column => $value],
            class_name: 'stdClass',
            number: 1
        );

        if ($row === null)
            return null;

        return new StatuteViolationReport(
            report_id: $row->report_id,
            software_id: $row->software_id,
            user_id: $row->user_id,
            rule_point: $row->rule_point,
            description: $row->description,
            date_added: $row->date_added,
            review_status: $row->review_status
        );
    }

    public function find_by_2_col(string $column1, mixed $value1,string $column2, mixed $value2): ?object {
        $row = $this->database->get_rows(
            query: "SELECT * FROM StatuteViolationReport WHERE $column1 = :$column1 AND $column2 = :$column2;",
            params: [$column1 => $value1, $column2 => $value2],
            class_name: 'stdClass',
            number: 1
        );

        if ($row === null)
            return null;

        return new StatuteViolationReport(
            report_id: $row->report_id,
            software_id: $row->software_id,
            user_id: $row->user_id,
            rule_point: $row->rule_point,
            description: $row->description,
            date_added: $row->date_added,
            review_status: $row->review_status
        );
    }
    private function in_database(StatuteViolationReport $statute_violation_report): bool {
        return $statute_violation_report->report_id !== null;
    }
    public function save(object $object): bool {
        if (!($object instanceof StatuteViolationReport))
            return false;

        if (!$this->in_database($object))
            return $this->insert($object);
        
        return $this->update($object);
    }

    public function insert(StatuteViolationReport $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:report_id, :software_id, :user_id, :rule_point, 
                    :description, :date_added, :review_status)",
            params: [
                'report_id' => $object->report_id?? NULL,
                'software_id' => $object->software_id,
                'user_id' => $object->user_id,
                'rule_point' => $object->rule_point,
                'description' => $object->description,
                'date_added' => is_string($object->date_added) ? $object->date_added : $object->date_added->format('Y-m-d H:i:s'),
                'review_status' => $object->review_status->value
            ]
        );
    }
    
    public function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE FROM $class WHERE report_id = :report_id;",
            params: ['report_id' => $id]
        );
    }

    public function update(StatuteViolationReport $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "UPDATE $created_class SET rule_point=:rule_point, 
                    description=:description, review_status=:review_status WHERE report_id=:report_id",
            params: [
                'report_id' => $object->report_id,
                'rule_point' => $object->rule_point,
                'description' => $object->description,
                'review_status' => $object->review_status->value
            ]
        );
    }
}