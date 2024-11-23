<?php

namespace App\Database;

use \Exception;
use \PDOException;
use \PDOStatement;

class QueryExecutor
{
    protected static function executeQuery(string $query, ?string $customErrorMessage = null, ?array $parameters = null): PDOStatement
    {
        $connection = Connection::getInstance();
        $pdoStatement = $connection->getConnection()->prepare($query);
        try {
            $parameters ? $pdoStatement->execute($parameters) : $pdoStatement->execute();
            return $pdoStatement;
        } catch (PDOException $e) {
            throw new Exception($customErrorMessage ? "Failed query: {$e->getMessage()}" : "{$customErrorMessage}: {$e->getMessage()}", $e->getMessage());
        } finally {
            $connection->closeConnection();
        }
    }
}
