<?php
require_once __DIR__ . '/AccountType.php';
require_once __DIR__ . '/AccountChangeRequest.php';

class User {

    public function __construct( 
        private ?int $user_id,
        private string $login,
        private string $password_hash,
        private string $username,
        private DateTime $account_creation_date,
        private AccountType $account_type
    ) {}

    public function change_password(string $password): void {
        $this->password_hash = password_hash($this->password_hash, Config::HASHING_ALGORITHM);
    }

    public function change_account_type(AccountType $account_type): void {
        $this->account_type = $account_type;
    }

    public function validate_password(string $password): bool {
        return password_verify($password, $this->password_hash);
    }

    public function generate_account_change_request(string $justification): AccountChangeRequest {
        if ($this->account_type != AccountType::CLIENT)
            throw new Exception("Only the clients may request a change to their account type.");

        return new AccountChangeRequest(
            request_id: null,
            user_id: $this->user_id,
            date_submitted: new DateTime(),
            review_status: RequestStatus::Pending,
            description: $justification
        );
    }

    public function __get(string $name): mixed {
        if (!property_exists($this, $name))
            throw new Exception("Property $name does not exist");
        return $this->$name;
    }

    public function __toString(): string {
        return "User: $this->username";
    }
}