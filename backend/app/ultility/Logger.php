<?php
require_once __DIR__.'/../config/Config.php';

class Logger {
    public static function log(string $message, Priority $priority): void {
        if (Config::LOG_MODE === 'file')
            self::file($message, $priority);
        else if (Config::LOG_MODE === 'echo')
            self::echo($message, $priority);
        else
            throw new Exception('Invalid log mode');
    }

    private static function echo(string $message, Priority $priority): void {
        $date = date('Y-m-d H:i:s');
        $priority = strval($priority);
        $message = "$date [$priority] $message\n";
        echo $message;
    }

    private static function file(string $message, Priority $priority): void {
        $date = date('Y-m-d H:i:s');
        $priority = strval($priority);
        $message = "$date [$priority] $message\n";
        file_put_contents(Config::LOG_FILE, $message, FILE_APPEND);
    }
}