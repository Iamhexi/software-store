<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../BugReport.php';

class BugReportRepository implements Repository {

    private Database $database;
    private const CLASS_NAME = 'BugReport';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }
    
    function find(int $id): ?BugReport {
        $created_class = self::CLASS_NAME;
        $row = $this->database->get_rows(
            query: "SELECT * FROM $created_class WHERE report_id = :report_id;",
            params: ['report_id' => $id],
            class_name: $created_class,
            number: 1
        );

        if ($row === null)
            return null;


        return new BugReport(
            report_id: $id,
            version_id: $row->version_id,
            user_id: $row->user_id,
            title: $row->title,
            description_of_steps_to_get_bug: $row->description_of_steps_to_get_bug,
            bug_description: $row->bug_description,
            date_added: $row->date_added,
            review_status: $row->review_status
        );
    }
    
    function find_all(): array {
        $created_class = self::CLASS_NAME;
        return $this->database->get_rows(
            query: "SELECT * FROM $created_class;",
            class_name: $created_class
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
                version_id: $row->version_id,
                user_id: $row->user_id,
                title: $row->title,
                description_of_steps_to_get_bug: $row->description_of_steps_to_get_bug,
                bug_description: $row->bug_description,
                date_added: new DateTime($row->date_added),
                review_status: $row->review_status
            );
        }

        return $objects ?? [];
    }
    
    public function save(object $object): bool {
        $created_class = self::CLASS_NAME;

        return $this->database->execute_query(
            query: "INSERT INTO $created_class VALUES (:report_id, :version_id, :user_id, :title, 
                    :description_of_steps_to_get_bug, :bug_description, :date_added, :review_status)",
            params: [
                'report_id' => $object->report_id?? "NULL",
                'version_id' => $object->version_id,
                'user_id' => $object->user_id,
                'title' => $object->title,
                'description_of_steps_to_get_bug' => $object->description_of_steps_to_get_bug,
                'bug_description' => $object->bug_description,
                'date_added' => $object->date_added,
                'review_status' => $object->review_status
            ]
        );
    }
    
    public function delete(int $id): bool {
        $class = self::CLASS_NAME;
        return $this->database->execute_query(
            query: "DELETE $class WHERE report_id = :report_id;",
            params: ['report_id' => $id]
        );
    }
}