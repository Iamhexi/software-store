<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../User.php';
require_once __DIR__.'/../../utility/Logger.php';
require_once __DIR__.'/../../Config.php';

class UserRepository implements Repository {

    private Database $database;
    private const CLASS_NAME = 'User';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find(int $id): ?User {
        $table = self::CLASS_NAME;
        $obj = $this->database->get_rows(
            query: "SELECT * FROM $table WHERE user_id = :user_id",
            params: ['user_id' => $id],
            class_name: $table,
            number: 1
        );

        
        if ($obj === null)
            return null;

    
        return new User(
            $obj->user_id,
            $obj->login,
            $obj->pass_hash,
            $obj->username,
            new DateTime($obj->account_creation_date),
            AccountType::fromString($obj->account_type)
        );
    
    }
        
    public function find_by(array $conditions): array {
        $class_name = self::CLASS_NAME;
        $allowed_columns = ['user_id', 'login', 'pass_hash', 'username', 'account_type', 'account_creation_date'];

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
                user_id : $row->user_id,
                login: $row->login,
                pass_hash: $row->pass_hash,
                username: $row->username,
                account_type: AccountType::from($row->account_type),
                account_creation_date: new DateTime($row->account_creation_date)
            );
        }

        return $objects ?? [];
    }
        

    public function save(object $object): bool {
        if (!($object instanceof User))
            return false;

        if (!$this->in_database($object))
            return $this->insert($object);
        
        return $this->update($object);
    }

    public function delete(int $id): bool {
        $table = self::$table_name;
        return $this->database->execute_query(
            query: "DELETE FROM $table WHERE user_id = :user_id",
            params: ['user_id' => $id]
        );
    }

    public function find_all(): array {
        $table = self::$table_name;
        $rows = $this->database->get_rows(
            query: "SELECT * FROM $table",
            class_name: 'stdClass'
        );

        foreach($rows as $row) {
            $users[] = new User(
                $row->user_id,
                $row->login,
                $row->pass_hash,
                $row->username,
                new DateTime($row->account_creation_date),
                AccountType::fromString($row->account_type)
            );
        }

        return $users ?? [];

    }

    private function in_database(User $user): bool {
        return $user->user_id !== null;
    }

    private function insert(User $user): bool {
        $table = self::$table_name;
        return $this->database->execute_query(
            query: "INSERT INTO $table VALUES (:user_id, :login, :pass_hash, :username, :account_creation_date, :account_type)",
            params: [
                'user_id' => $user->user_id ?? NULL,
                'login' => $user->login,
                'pass_hash' => $user->pass_hash,
                'username' => $user->username,
                'account_creation_date' => is_string($user->account_creation_date) ? $user->account_creation_date : $user->account_creation_date->format('Y-m-d H:i:s'),
                'account_type' => $user->account_type->value
            ]
        );
    }

    private function update(User $user): bool {
        $table = self::$table_name;
        return $this->database->execute_query(
            query: "UPDATE $table SET login = :login, pass_hash = :pass_hash, username = :username, account_type = :account_type WHERE user_id = :user_id",
            params: [
                'user_id' => $user->user_id,
                'login' => $user->login,
                'pass_hash' => $user->pass_hash,
                'username' => $user->username,
                'account_type' => $user->account_type->value
            ]
        );
    }

}