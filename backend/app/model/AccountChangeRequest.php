<?php 
require __DIR__.'/../Config.php';
class AccountChangeRequest {
    public function __construct(
        private ?int $request_id,
        private int $user_id,
        private string $description,
        private string $justification,
        private DateTime $date_submitted = new DateTime,
        private string $review_status
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        if ($propertyName === 'date_submitted')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);
        
        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'Description: '. $this->description . ' ; ' . $this->justification . ':'. $this->review_status;
    }
}