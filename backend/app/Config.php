<?php

class Config {
    public const DATABASE_HOST = 'localhost';
    public const DATABASE_USER = 'Administrator';
    public const DATABASE_NAME = 'software_store';
    public const DATABASE_PASSWORD = 'your_password';
    public const LOG_FILE = __DIR__.'/../logs.txt';
    public const LOG_MODE = 'file'; // 'file', 'echo'
    public const HASHING_ALGORITHM = PASSWORD_ARGON2ID;
    public const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';
    public const EXPIRATION_TIME_IN_SECONDS = 3600;
    public const AUTH_TOKEN_LENGTH = 128;
    public const TIME_ZONE = 'Europe/Warsaw';
    public const WEB_URL = 'http://localhost:9999';
}