<?php

namespace Webtek\Core\Database;

use PDO;

class DBConnection {

    private mixed $pdo;

    public function __construct()
    {
        $host = 'localhost';
        $db   = 'db_bit_traders';
        $user = 'root';
        $pass = 'password';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->setPdo(new PDO($dsn, $user, $pass, $options));
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * @return mixed
     */
    public function getPdo(): mixed
    {
        return $this->pdo;
    }

    /**
     * @param mixed $pdo
     */
    public function setPdo($pdo): void
    {
        $this->pdo = $pdo;
    }
}