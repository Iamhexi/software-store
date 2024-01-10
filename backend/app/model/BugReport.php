<?php
require_once __DIR__.'/PDODatabase.php';

class BugReport {

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

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");
        
        if ($propertyName === 'date_added')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);
        
        return $this->$propertyName;
    }

    public function __toString(): string {
        return "BugReport: $this->title : $this->description_of_steps_to_get_bug : $this->review_status";
    }
}