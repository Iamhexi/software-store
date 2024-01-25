<?php
require_once __DIR__.'/JsonSerializableness.php';
require_once __DIR__.'/AccountType.php';

class Authority implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private int $user_id,
        private ?AccountType $account_type
    ) {}

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __set(string $property, mixed $value): void {
        if (!property_exists($this, $property))
            throw new Exception("Property $property does not exist");
    }
}