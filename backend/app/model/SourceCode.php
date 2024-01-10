<?php 
require __DIR__.'/../Config.php';
class SourceCode {
    public function __construct(
        private ?int $code_id,
        private int $version_id,
        private string $filepath
    ) {}

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");
        
        return $this->$propertyName;
    }

    public function __toString(): string {
        return 'FilePath: ' . $this->filepath;  
    }
}