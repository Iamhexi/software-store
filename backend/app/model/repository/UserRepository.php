<?php 
require_once __DIR__.'/Repository.php';
require_once __DIR__.'/../PDODatabase.php';
require_once __DIR__.'/../User.php';

class UserRepository implements Repository {

    private Database $database;
    static string $table_name = 'User';

    public function __construct() {
        $this->database = new PDODatabase;
    }

    public function find($id): ?User {
        $table = self::$table_name;
        return $this->database->get_rows(
            query: "SELECT * FROM $table WHERE user_id = :user_id",
            params: ['user_id' => $id],
            class_name: self::$table_name,
            number: 1
        );
        
    }

    public function findAll(): array {
        $table = self::$table_name;
        return $this->database->get_rows(
            query: "SELECT * FROM $table",
            class_name: self::$table_name
        );
    }

    public function save(User $object): bool {
        $table = self::$table_name;

        return $this->database->execute_query(
            query: "INSERT INTO $table VALUES (:user_id, :login, :password_hash, :username, :account_creation_date, :account_type)",
            params: [
                'user_id' => $object->user_id ?? "NULL",
                'login' => $object->login,
                'password_hash' => $object->password_hash,
                'username' => $object->username,
                'account_creation_date' => $object->account_creation_date,
                'account_type' => $object->account_type
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