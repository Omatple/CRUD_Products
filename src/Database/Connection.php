<?php

namespace App\Database;

use \Dotenv\Dotenv;
use \Exception;
use \PDO;
use \PDOException;
use \RuntimeException;

class Connection
{
    private static ?Connection $instance = null;
    private ?PDO $connection = null;

    private function __construct()
    {
        $this->initializeInstance();
    }

    public static function getInstance(): Connection
    {
        if (self::$instance === null) {
            self::$instance = new Connection();
        }
        return self::$instance;
    }

    private function initializeInstance()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
        $dotenv->load();
        foreach (["USER", "PASSWORD", "HOST", "PORT", "DBNAME"] as $key) {
            if (!isset($_ENV[$key])) throw new Exception("Error Processing .Env");
        }
        $dsn = "mysql:dbname={$_ENV['DBNAME']};host={$_ENV['HOST']};port={$_ENV['PORT']};charset=utf8mb4";
        try {
            $this->connection = new PDO($dsn, $_ENV['USER'], $_ENV['PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException("Database connection failed: {$e->getMessage()}", (int) $e->getCode());
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->connection;
    }

    public function closeConnection(): void
    {
        $this->connection = null;
        self::$instance = null;
    }
}
