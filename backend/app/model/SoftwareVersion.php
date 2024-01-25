<?php
require_once __DIR__.'/JsonSerializableness.php';
require_once __DIR__.'/Version.php';
require_once __DIR__.'/SourceCode.php';

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

    public function generate_source_code(): SourceCode {
        $filepath = __DIR__ . '/../../resources/source_codes/' . strval( $this->software_id ) . '_' . strval($this->version) . '_' . strval( rand(0, 99999999));
        return new SourceCode(null, $this->version_id, '/' . $this->get_absolute_path($filepath));
    }

    private function get_absolute_path(string $path): string {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
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