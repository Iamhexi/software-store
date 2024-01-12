<?php


enum AccountType: string {
    case ADMIN = 'administrator';
    case SOFTWARE_AUTHOR = 'author';
    case CLIENT = 'client';
    case INCORRECT = '';

    public static function fromString(string $account_type): AccountType {
        switch ($account_type) {
            case 'administrator':
                return self::ADMIN;
            case 'author':
                return self::SOFTWARE_AUTHOR;
            case 'client':
                return self::CLIENT;
            default:
                return self::INCORRECT;
        }
    }
}
