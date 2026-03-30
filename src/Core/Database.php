<?php

declare(strict_types=1);

namespace Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static ?array $config = null;
    
    private static function initConfig(): void
    {
        if (self::$config === null) {
            self::$config = require __DIR__ . '/../../config/database.php';
        }
    }
    
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            self::initConfig();
            
            try {
                self::$instance = new PDO(
                    self::$config['dsn'],
                    self::$config['user'],
                    self::$config['password'],
                    self::$config['options']
                );
            } catch (PDOException $e) {
                error_log($e->getMessage());
                throw new \Exception('Erreur de connexion à la base de données');
            }
        }
        
        return self::$instance;
    }
    
    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}
}
