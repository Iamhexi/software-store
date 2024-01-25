<?php
require_once __DIR__.'/JsonSerializableness.php';

class Version implements JsonSerializable {
    use JsonSerializableness;
    
    public function __construct(
        private int $major,
        private int $minor,
        private ?int $patch
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        return $this->$propertyName;
    }

    public function __toString(): string {
        $patch = $this->patch == null ? '0' : strval($this->patch);
        return strval($this->major) . '_' . strval($this->minor) . '_' . $patch;
    }
}