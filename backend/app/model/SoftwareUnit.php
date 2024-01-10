<?php 
require __DIR__.'/../Config.php';
class SoftwareUnit {
    public function __construct(
        private ?int $software_id,
        private int $author_id,
        private string $name,
        private string $description,
        private string $link_to_graphic,
        private int $is_blocked,
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'Name: '. $this->name . ' ; ' . $this->description;
    }
}