<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../User.php';
require_once __DIR__.'/../../utility/Logger.php';
require_once __DIR__.'/../../Config.php';

class UserRepository implements Repository {

    private Database $database;
    static string $table_name = 'User';

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find(int $id): ?User {
        $table = self::$table_name;
        $obj = $this->database->get_rows(
            query: "SELECT * FROM $table WHERE user_id = :user_id",
            params: ['user_id' => $id],
            class_name: self::$table_name,
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

    public function find_by(string $column, mixed $value): ?object {
        $table = self::$table_name;

        if (!property_exists(self::$table_name, $column)) {
            Logger::log("Column $column does not exist in table $table", Priority::ERROR);
            return null;
        }

        if ($column === 'user_id')
            return $this->find($value);


        $obj = $this->database->get_rows(
            query: "SELECT * FROM $table WHERE $column = :value",
            params: ['value' => $value],
            class_name: self::$table_name,
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