<?php
require_once __DIR__.'/JsonSerializableness.php';

class Token implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private string $token,
        private int $user_id,
        private DateTime $expires_at
    ) {}

    public function is_valid(): bool {
        return $this->expires_at->getTimestamp() > time();
    }

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        date_timezone_set($this->expires_at, timezone_open('Europe/Warsaw'));
        return $this->token . '/USER:' . $this->user_id . '/TIME:' . $this->expires_at->format(Config::DB_DATETIME_FORMAT);
    }
}