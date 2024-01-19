<?php
require_once __DIR__.'/JsonSerializableness.php';
require_once __DIR__.'/../Config.php';

class SoftwareUnit implements JsonSerializable {
    use JsonSerializableness;
  
    public function __construct(
        private ?int $software_id,
        private int $author_id,
        private string $name,
        private string $description,
        private string $link_to_graphic,
        private bool $is_blocked
    ) {}

    public function block(): void {
        $this->is_blocked = true;
    }

    public function unblock(): void {
        $this->is_blocked = false;
    }

    public function release_new_version(string $description, int $major, int $minor): SoftwareVersion {
        return new SoftwareVersion(
            version_id: null,
            software_id: $this->software_id,
            description: $description,
            date_added: new DateTime(),
            major_version: $major,
            minor_version: $minor,
            patch_version: NUll
        );
    }

    private function is_graphic_link_valid(string $link): bool {
        return filter_var($link, FILTER_VALIDATE_URL)
            && preg_match('/\.(png|jpg|jpeg|gif|webp)$/i', $link);
    }

    public function __set(string $name, mixed $value): void {
        if ($name == 'software_id')
            throw new Exception("Cannot change the software_id");
        else if ($name == 'link_to_graphic') {
            $this->is_graphic_link_valid($value)
                ? $this->link_to_graphic = $value
                : throw new Exception("Invalid graphic link. Must be a valid URL to an image file ending with jpg, png et cetera.");
        }

        else if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        $this->$name = $value;
    }

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        return "SoftwareUnit: {$this->name}";
    }
}