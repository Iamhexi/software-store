
<?php 
require_once __DIR__.'/../Config.php';
class Category implements JsonSerializable {
    use JsonSerializableness;
    
    public function __construct(
        private ?int $category_id,
        private string $name,
        private string $description
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        return $this->$propertyName;
    }

    public function __set(string $name, mixed $value): void {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        $this->$name = $value;
    }

    public function __toString(): string {
        return 'Name: ' . $this->name . ' ; ' . $this->description;
    }
}