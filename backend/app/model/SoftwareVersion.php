<?php
require_once __DIR__.'/JsonSerializableness.php';

class SoftwareVersion implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private ?int $version_id,
        private int $software_id,
        private string $description,
        private DateTime $date_added,
        private int $major_version,
        private int $minor_version,
        private ?int $patch_version
    ) {}

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        return "SoftwareVersion: {$this->major_version}.{$this->minor_version}.{$this->patch_version}";
    }
}