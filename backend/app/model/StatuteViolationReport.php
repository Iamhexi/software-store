<?php 
require_once __DIR__.'/../Config.php';
class StatuteViolationReport implements JsonSerializable{
    use JsonSerializableness;
    public function __construct(
        private ?int $report_id,
        private int $software_id,
        private int $user_id,
        private int $rule_point,
        private string $description,
        private DateTime $date_added,
        private RequestStatus $review_status
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        if ($propertyName === 'date_added')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);     

        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'Description: '. $this->description . ' ; ' . $this->review_status;
    }
}