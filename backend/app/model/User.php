<?php
require_once __DIR__.'/PDODatabase.php';
class User {

    public function __construct( 
        private ?int $user_id,
        private string $login,
        private string $password_hash,
        private string $username,
        private DateTime $account_creation_date,
        private AccountType $account_type
    ) {
        $this->user_id = $user_id;
        $this->login = $login;
        $this->password_hash = $password_hash;
        $this->username = $username;
        $this->account_creation_date = $account_creation_date;
        $this->account_type = $account_type;
    }

    public function register(): bool {
        $database = new PDODatabase();
        $query = 'INSERT INTO users(login, password_hash, username, account_creation_date, account_type_id) VALUES (?, ?, ?, ?, ?)';
        $params = [$this->login, $this->password_hash, $this->username, $this->account_creation_date->format('Y-m-d H:i:s'), strval($this->account_type)];
        return $database->execute_query($query, $params);
    }

    // TODO: implement business logic of User

}