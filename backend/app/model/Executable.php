<?php 
require_once __DIR__.'/../Config.php';
require_once __DIR__.'/JsonSerializableness.php';

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

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");
        
        if ($propertyName === 'date_compiled')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);
        else if ($propertyName === 'target_architecture')
            return $this->target_architecture->value;
        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'FilePath: ' . $this->filepath . ' ; ' . $this->target_architecture;
    }
}