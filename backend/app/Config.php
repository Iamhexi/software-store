<?php

class Config {
    const DATABASE_HOST = 'localhost';
    const DATABASE_USER = 'Administrator';
    const DATABASE_NAME = 'software_store';
    const DATABASE_PASSWORD = 'your_password';
    const LOG_FILE = __DIR__.'/../logs.txt';
    const LOG_MODE = 'echo'; // 'file', 'echo'

    const HASHING_ALGORITHM = PASSWORD_ARGON2ID;
}