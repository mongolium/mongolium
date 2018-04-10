<?php

namespace Mongolium\Core\Services\Db;

use MongoDB\Client as MongoClient;
use MongoDB\Collection as MongoCollection;

/**
 *
 *
 * @author Rob Waller <rdwaller1984@googlemail.com>
 */
final class Client
{
    /**
     * @var Client $instance
     */
    private static $instance;

    /**
     * @var MongoClient $connection
     */
    private $connection;

    /**
     * @var string $database
     */
    private $database;

    /**
     * Constructor set to private as class follows singleton pattern.
     *
     * @param string $host
     * @param string $port
     * @param string $database
     */
    private function __construct(string $host, string $port, string $database)
    {
        $this->connection = new MongoClient('mongodb://' . $host . ':' . $port);

        $this->database = $database;
    }

    /**
     * Return an instance of the client, this class follows a singleton
     * pattern so only one client connection is ever created.
     *
     * @param string $host
     * @param string $port
     * @param string $database
     * @return Client
     */
    public static function getInstance(string $host, string $port, string $database): Client
    {
        if (null === static::$instance) {
            static::$instance = new static($host, $port, $database);
        }
        return static::$instance;
    }

    /**
     * Return the mongo client so connections can be made to the database.
     *
     * @return MongoClient
     */
    public function getConnection(): MongoClient
    {
        return $this->connection;
    }

    /**
     * Return the database name to be used for the mongo connection
     *
     * @return string
     */
    public function getDatabase(): string
    {
        return $this->database;
    }

    /**
     * Retrieve Mongo collection by name
     *
     * @param string $name
     * @return MongoCollection
     */
    public function getCollection(string $name): MongoCollection
    {
        return $this->getConnection()->{$this->getDatabase()}->{$name};
    }
}
