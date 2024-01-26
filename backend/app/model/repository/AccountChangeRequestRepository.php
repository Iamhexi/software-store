<?php
require_once __DIR__ . '/Repository.php';
require_once __DIR__ . '/../AccountChangeRequest.php';

class AccountChangeRequestRepository {

    private Database $database;
    private const CLASS_NAME = 'AccountChangeRequest';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find(int $id): ?object {
        return $this->database->get_rows(
            query: "SELECT * FROM AccountChangeRequest WHERE request_id = :request_id;",
            params: ['request_id' => $id],
            class_name: 'AccountChangeRequest',
            number: 1
        );
    }

    public function find_by(array $conditions): array {
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
                request_id: $row->request_id,
                user_id: $row->user_id,
                description: $row->description,
                justification: $row->justification,
                date_submitted: new DateTime($row->date_submitted),
                review_status: $row->review_status === 0 ? RequestStatus::Pending : RequestStatus::convert_string_to_request_status($row->review_status)
            );
        }

        return $objects ?? [];
    }

    public function find_all(): array {
        $class_name = self::CLASS_NAME;

        $rows = $this->database->get_rows(
            query: "SELECT * FROM AccountChangeRequest;",
            class_name: 'stdClass'
        );

        foreach ($rows as $row) {

            $objects[] = new $class_name(
                request_id: $row->request_id,
                user_id: $row->user_id,
                description: $row->description,
                justification: $row->justification,
                date_submitted: new DateTime($row->date_submitted),
                review_status: RequestStatus::convert_string_to_request_status($row->review_status)
            );
        }

        return $objects ?? [];
    }

    private function exists_in_database(AccountChangeRequest $object): bool {
        return $this->find($object->request_id) !== null;
    }

    private function update(AccountChangeRequest $object): bool {
        return $this->database->execute_query(
            query: "UPDATE AccountChangeRequest SET user_id = :user_id, description = :description, justification = :justification, date_submitted = :date_submitted, review_status = :review_status WHERE request_id = :request_id;",
            params: [
                'request_id' => $object->request_id,
                'user_id' => $object->user_id,
                'description' => $object->description,
                'justification' => $object->justification,
                'date_submitted' => $object->date_submitted->format('Y-m-d'),
                'review_status' => $object->review_status->value
            ]
        );
    }

    private function insert(AccountChangeRequest $object): bool {
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

    public function save(AccountChangeRequest $object): bool {
        if ($this->exists_in_database($object))
            return $this->update($object);
        return $this->insert($object);
    }

    public function delete(int $id): bool {
        return $this->database->execute_query(
            query: "DELETE AccountChangeRequest WHERE request_id = :request_id;",
            params: ['request_id' => $id]
        );
    }
  
}