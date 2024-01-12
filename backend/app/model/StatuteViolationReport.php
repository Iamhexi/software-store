<?php 

class StatuteViolationReport { // Data Transfer Object
    public function __construct(
        public ?int $report_id,
        public int $software_id,
        public string $description,
        public int $rule_point,
        public DateTime $date_added,
        public string $review_status
    ) {}

    public function __get(string $name): mixed {
        if ($name === 'date_added')
            return $this->date_added->format('Y-m-d H:i:s');
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }
}