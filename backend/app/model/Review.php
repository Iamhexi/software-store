<?php 
require __DIR__.'/../Config.php';
class Review implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private ?int $review_id,
        private int $author_id,
        private int $software_id,
        private string $title,
        private string $description,
        private DateTime $date_added = new DateTime,
        private DateTime $date_last_updated = new DateTime
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        if ($propertyName === 'date_added' || $propertyName === 'date_last_updated')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);    
        
        return $this->$propertyName;
    }

    public function __toString(): string {
        return $this->title . ': ' . $this->description;
    }
}