<?php
require_once __DIR__.'/JsonSerializableness.php';   

class Download implements JsonSerializable { 
    use JsonSerializableness;

    public function __construct(
        private ?int $download_id,
        private int $user_id,
        private int $executable_id,
        private DateTime $download_date
    ) {}

    public function __get(string $property): mixed {
        if (!property_exists($this, $property))
            throw new Exception("Property $property does not exist");
        return $this->$property;
    }

    public function __set(string $property, int|DateTime $value): void {
        if (!property_exists($this, $property))
            throw new Exception("Property $property does not exist");
        else if ($property === 'download_date' && is_int($value)) {
            $date = new DateTime();
            $value = $date->setTimestamp($value);
        }

        $this->$property = $value;
    }
}