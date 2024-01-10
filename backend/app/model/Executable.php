<?php 
require __DIR__.'/../Config.php';
class Executable {
    public function __construct(
        private ?int $executable_id,
        private int $version_id,
        private string $target_architecture,
        private DateTime $date_compiled,
        private string $filepath
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");
        
        if ($propertyName === 'date_compiled')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);
        
        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'FilePath: ' . $this->filepath . ' ; ' . $this->target_architecture;
    }
}