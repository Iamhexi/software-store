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

    public function __set(string $property, int|DateTime $value): void {
        if (!property_exists($this, $property))
            throw new Exception("Property $property does not exist");
        else if ($property === 'download_date' && is_int($value)) {
            $date = new DateTime();
            $value = $date->setTimestamp($value);
        }
    }

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");
    
        if ($propertyName === 'date_download')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);
        
        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'Date_download: ' . $this->date_download;
    }
}