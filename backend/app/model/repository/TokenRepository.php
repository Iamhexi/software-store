<?php
require_once __DIR__ . '/../Token.php';
require_once __DIR__ . '/../PDODatabase.php';
require_once __DIR__ . '/Repository.php';

class TokenRepository implements Repository {

    private Database $database;

    public function __construct(Database $database = new PDODatabase) {
        $this->database = $database;
    }

    public function find_by(string $column, mixed $value): ?object {
        return null;
    }

    public function find(string|int $id): ?Token {
        $obj = $this->database->get_rows(
            query: "SELECT * FROM Token WHERE token = :token",
            params: ['token' => $id], // implement token expiration in SQL
            class_name: 'Token',
            number: 1
        );

        if ($obj === null) // reject if token is expired
            return null;

        $date = new DateTime($obj->expires_at, new DateTimeZone('Europe/Warsaw'));

        return new Token(
            token: $obj->token,
            user_id: $obj->user_id,
            expires_at: $date
        );
    }

    public function find_all(): array {
        return $this->database->get_rows(
            query: "SELECT * FROM Token",
            class_name: 'Token'
        );
    }

    public function save(object $token): bool {
        if (!($token instanceof Token))
            return false;
        date_timezone_set($token->expires_at, timezone_open('Europe/Warsaw'));
        $date = $token->expires_at->format(Config::DB_DATETIME_FORMAT);

        return $this->database->execute_query(
            query: "INSERT INTO Token (token, user_id, expires_at) VALUES (:token, :user_id, :expires_at)",
            params: [
                'token' => $token->token,
                'user_id' => $token->user_id,
                'expires_at' => $date
            ]
        );
    }

    public function delete(int|string $token): bool {
        return $this->database->execute_query(
            query: "DELETE FROM Token WHERE token = :token",
            params: [
                'token' => strval($token)
            ]
        );
    }
}