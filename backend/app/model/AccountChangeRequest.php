<?php 
require_once __DIR__ . '/RequestStatus.php';
require_once __DIR__ . '/JsonSerializableness.php';

class AccountChangeRequest implements JsonSerializable { // Data Transfer Object
    use JsonSerializableness;

    public function __construct(
        public ?int $request_id,
        public int $user_id,
        public string $description,
        public string $justification = '',
        public DateTime $date_submitted,
        public RequestStatus $review_status
    ) {}

    public function __toString(): string {
        return "AccountChangeRequest: $this->request_id";
    }

    public function __get(string $name): mixed {
        if ($name === 'review_status')
            return $this->review_status->value;
        else if ($name === 'date_submitted')
            return $this->date_submitted->format('Y-m-d H:i:s');
        else if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }
}