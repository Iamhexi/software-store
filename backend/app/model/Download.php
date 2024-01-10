<?php 
require __DIR__.'/../Config.php';
class Download {
    public function __construct(
        private ?int $download_id,
        private int $user_id,
        private int $executable_id,
        private DateTime $date_download
    ) {}

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