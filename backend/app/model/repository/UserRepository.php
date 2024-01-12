<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../User.php';
require_once __DIR__.'/../../utility/Logger.php';
require_once __DIR__.'/../../Config.php';

class UserRepository implements Repository {

    private Database $database;
    static string $table_name = 'User';

    public function __construct() {
        $this->database = new PDODatabase;
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

    public function find_by(string $column, mixed $value): ?User {
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

    public function findAll(): array {
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

    public function save(object $object): bool {
        $table = self::$table_name;
        if ($object->user_id === null)
            return $this->database->execute_query(
                query: "INSERT INTO $table VALUES (:user_id, :login, :pass_hash, :username, :account_creation_date, :account_type)",
                params: [
                    'user_id' => $object->user_id ?? "NULL",
                    'login' => $object->login,
                    'pass_hash' => $object->pass_hash,
                    'username' => $object->username,
                    'account_creation_date' => $object->account_creation_date->format('Y-m-d H:i:s'),
                    'account_type' => $object->account_type->value
                ]
            );
        
        return $this->database->execute_query(
            query: "UPDATE $table SET login = :login, pass_hash = :pass_hash, username = :username, account_creation_date = :account_creation_date, account_type = :account_type WHERE user_id = :user_id",
            params: [
                'user_id' => $object->user_id,
                'login' => $object->login,
                'pass_hash' => $object->pass_hash,
                'username' => $object->username,
                'account_creation_date' => $object->account_creation_date,
                'account_type' => $object->account_type->value
            ]
        );
    }

    public function delete(int $id): bool {
        $table = self::$table_name;
        return $this->database->execute_query(
            query: "DELETE FROM $table WHERE user_id = :user_id",
            params: ['user_id' => $id]
        );
    }
    
}