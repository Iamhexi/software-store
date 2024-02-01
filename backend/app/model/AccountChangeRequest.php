<?php 
require_once __DIR__ . '/RequestStatus.php';
require_once __DIR__ . '/../Config.php';
require_once __DIR__ . '/JsonSerializableness.php';

class AccountChangeRequest implements JsonSerializable { // Data Transfer Object
    use JsonSerializableness;

    public function __construct(
        public ?int $request_id,
        public int $user_id,
        public string $description,
        public string $justification = '',
        public string|DateTime $date_submitted,
        public string|RequestStatus $review_status
    ) {
        if (is_string($date_submitted))
            $date_submitted = new DateTime($date_submitted);
        $this->date_submitted = $date_submitted;

        if (is_string($review_status))
            $review_status = RequestStatus::from($review_status);
    }

    public function __toString(): string {
        return "AccountChangeRequest: $this->request_id";
    }

    public function __get(string $name): mixed {
        if ($name === 'review_status')
            return $this->review_status->value;
        else if ($name === 'date_submitted')
            return $this->date_submitted->format(Config::DB_DATE_FORMAT);
        else if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }
}