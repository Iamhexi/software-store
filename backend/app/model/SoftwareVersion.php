<?php
require_once __DIR__.'/JsonSerializableness.php';
require_once __DIR__.'/Version.php';

class SoftwareVersion implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private ?int $version_id,
        private int $software_id,
        private string $description,
        private DateTime $date_added,
        private Version $version
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        if ($propertyName === 'date_added')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);

        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'Software_id: '. $this->software_id . ' ; ' . $this->description . 
        ' ; ' . $this->major_version . '.' . $this->minor_version . '.' . $this->patch_version;
    }
    public function __properties(): array {
        return array_keys(get_object_vars($this));
    }
    
    public static function getPropertyNames() : array {
        $rating = new SoftwareVersion(1, 0, '', new DateTime(), new Version(1,1,1));
        return $rating->__properties();
    }
}