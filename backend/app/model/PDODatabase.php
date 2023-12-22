<?php
require_once __DIR__.'/Database.php';
require_once __DIR__.'/../Config.php';

class PDODatabase implements Database {   
    private ?PDO $pdo = null;

    public function __construct() {
        $database_host = Config::DATABASE_HOST;
        $database_name = Config::DATABASE_NAME;

        $this->pdo ?? new PDO(
            "mysql:host=$database_host;dbname=$database_name;charset=utf8",
            Config::DATABASE_USER,
            Config::DATABASE_PASSWORD
            );
    }
    
    public function execute_query(string $query, array $params = []): bool {
        try {
            $stmt = $this->pdo->prepare($query);
            $result = $stmt->execute($params);
        } catch (PDOException $e) {
            Logger::log($e->getMessage(), Priority::ERROR);
            return false;
        }
        
        return $result;
    }

    public function get_rows(string $query, array $params = [], string $class_name = 'stdClass', int $number = PHP_INT_MAX): array|User|null {

        $statement = $this->pdo->prepare($query);
        $statement->execute($params);
        
        $statement->setFetchMode(PDO::FETCH_CLASS, $class_name);
        
        if ($number === PHP_INT_MAX)
            $result = $statement->fetchAll();
        else {
            $result = [];
            for($i = 0; $i < $number; $i++)
                $result[] = $statement->fetchObject();
        }
        
        return $result;
    }

}