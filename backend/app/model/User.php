<?php
require_once __DIR__ . '/AccountType.php';
require_once __DIR__ . '/AccountChangeRequest.php';
require_once __DIR__ . '/JsonSerializableness.php';

class User implements JsonSerializable {
    use JsonSerializableness;

    public function __construct( 
        private ?int $user_id = null,
        private string $login,
        private string $pass_hash,
        private string $username,
        private string|DateTime $account_creation_date = new DateTime(),
        private AccountType $account_type
    ) {}

    public function change_password(string $password): void {
        $this->pass_hash = password_hash($password, Config::HASHING_ALGORITHM);
    }

    public function change_account_type(AccountType $account_type): void {
        $this->account_type = $account_type;
    }

    public function validate_password(string $password): bool {
        return password_verify($password, $this->pass_hash);
    }

    public function generate_account_change_request(string $justification): AccountChangeRequest {
        if ($this->account_type != AccountType::CLIENT)
            throw new Exception("Only the clients may request a change to their account type.");

        return new AccountChangeRequest(
            request_id: null,
            user_id: $this->user_id,
            date_submitted: new DateTime(),
            justification: '',
            review_status: RequestStatus::Pending,
            description: $justification
        );
    }

    public function __get(string $propertyName): mixed {
        if (!property_exists($this, $propertyName))
            throw new Exception("Property $propertyName does not exist");

        if ($propertyName === 'account_creation_date')
            return $this->$propertyName->format(Config::DB_DATETIME_FORMAT);

        return $this->$propertyName;
    }

    public function __set(string $name, mixed $value): void {
        if ($name == 'account_creation_date' && $value !== null && !($value instanceof DateTime))
            $this->account_creation_date = new DateTime($value);
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        $this->$name = $value;
    }

    public function __toString(): string {
        return "User: $this->username";
    }

}