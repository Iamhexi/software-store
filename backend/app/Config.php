<?php

class Config {
    const DATABASE_HOST = 'localhost';
    const DATABASE_USER = 'Administrator';
    const DATABASE_NAME = 'software_store';
    const DATABASE_PASSWORD = 'your_password';
    const LOG_FILE = __DIR__.'/../logs.txt';
    const LOG_MODE = 'file'; // 'file', 'echo'
    const HASHING_ALGORITHM = PASSWORD_ARGON2ID;
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';
}