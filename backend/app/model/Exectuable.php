$pdoMock->method()->
<?php
require_once __DIR__.'/JsonSerializableness.php';
require_once __DIR__.'/Architecture.php';

class Executable implements JsonSerializable {
    use JsonSerializableness;

    public function __construct(
        private ?int $executable_id,
        private int $version_id,
        private string|Architecture $target_architecture,
        private DateTime $date_compiled,
        private string $filepath
    ) {
        if (is_string($target_architecture))
            $this->target_architecture = Architecture::from($target_architecture);
    }

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        return "Executable: $this->filepath";
    }
}