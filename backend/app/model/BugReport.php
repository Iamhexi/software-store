<?php
require_once __DIR__.'/PDODatabase.php';
require_once __DIR__.'/JsonSerializableness.php';

class BugReport implements JsonSerializable {
    use JsonSerializableness;

    public function __construct( 
        private ?int $report_id,
        private int $version_id,
        private int $user_id,
        private string $title,
        private string $description_of_steps_to_get_bug,
        private string $bug_description,
        private DateTime $date_added,
        private string $review_status
    ) {}

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        return "BugReport: $this->title : $this->description_of_steps_to_get_bug : $this->review_status";
    }
}