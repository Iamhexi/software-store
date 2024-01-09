<?php
require_once __DIR__.'/PDODatabase.php';

class Rating {

    public function __construct( 
        private ?int $rating_id,
        private int $author_id,
        private int $software_id,
        private int $mark,
        private DateTime $date_added
    ) {}

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        return "Rating: $this->mark";
    }
}