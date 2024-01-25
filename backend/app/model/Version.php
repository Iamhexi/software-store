<?php
require_once __DIR__.'/JsonSerializableness.php';

class Version implements JsonSerializable {
    use JsonSerializableness;
    
    public function __construct(
        private int $major_version,
        private int $minor_version,
        private ?int $patch_version,
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        return $this->$propertyName;
    }

    public function __toString(): string {
        return $this->major_version . '.' . $this->minor_version . '.' . $this->patch_version;
    }

    public function __properties(): array {
        return array_keys(get_object_vars($this));
    }
    
    public static function getPropertyNames() : array {
        $rating = new Version(1,1,1);
        return $rating->__properties();
    }
}