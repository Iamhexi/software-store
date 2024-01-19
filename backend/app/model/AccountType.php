<?php

enum AccountType: string {
    case ADMIN = 'admin';
    case SOFTWARE_AUTHOR = 'author';
    case CLIENT = 'client';
    case GUEST = 'guest';

    public static function fromString(string $account_type): AccountType {
        switch ($account_type) {
            case 'admin':
                return self::ADMIN;
            case 'author':
                return self::SOFTWARE_AUTHOR;
            case 'client':
                return self::CLIENT;
            case 'guest':
            default:
                return self::GUEST;
        }
    }
}
