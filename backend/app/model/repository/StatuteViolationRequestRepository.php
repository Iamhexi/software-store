<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../StatuteViolationRequest.php';

class StatuteViolationRequestRepository {

    private Database $database;
    private const CLASS_NAME = 'StatuteViolationRequest';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    function find(int $id): ?StatuteViolationRequest {
        return $this->database->get_rows(
            query: "SELECT * FROM StatuteViolationRequest WHERE report_id = :report_id;",
            params: ['report_id' => $id],
            class_name: 'StatuteViolationRequest',
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
                report_id: $row->report_id,
                software_id: $row->software_id,
                description: $row->description,
                rule_point: $row->rule_point,
                date_added: new DateTime($row->date_added),
                review_status: RequestStatus::from($row->review_status)
            );
        }

        return $objects ?? [];
    }

    function find_all(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM StatuteViolationRequest;",
            class_name: 'StatuteViolationRequest'
        );
    }

    function save(StatuteViolationRequest $object): bool {
        return $this->database->execute_query(
            query: "INSERT INTO StatuteViolationRequest VALUES (:report_id, :software_id, :description, :rule_point, :date_added, :review_status)",
            params: [
                'report_id' => $object->report_id ?? NULL,
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