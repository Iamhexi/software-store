<?php
require_once __DIR__.'/PDODatabase.php';
require_once __DIR__.'/JsonSerializableness.php';

class Rating implements JsonSerializable {
    use JsonSerializableness;

    public function __construct( 
        private ?int $rating_id,
        private int $author_id,
        private int $software_id,
        private int $mark,
        private DateTime $date_added
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        if ($propertyName === 'date_added')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);

        return $this->$propertyName;
    }

    public function __toString(): string {
        return "Rating: $this->mark";
    }
}