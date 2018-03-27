<?php

namespace Mongolium\Core\Services\Db;

use MongoDB\Client as MongoClient;

final class Client
{
    private static $instance;

    private $connection;

    private $database;

    private function __construct(string $host, string $port, string $database)
    {
        $this->connection = new MongoClient('mongodb://' . $host . ':' . $port);

        $this->database = $database;
    }

    public static function getInstance(string $host, string $port, string $database): Client
    {
        if (null === static::$instance) {
            static::$instance = new static($host, $port, $database);
        }
        return static::$instance;
    }

    public function getConnection(): MongoClient
    {
        return $this->connection;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }
}
