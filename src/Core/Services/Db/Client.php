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
     * @param string $username
     * @param string $password
     */
    private function __construct(string $host, string $port, string $database, string $username, string $password)
    {
        $this->connection = $this->makeConnection($host, $port, $database, $username, $password);

        $this->database = $database;
    }

    /**
     * Make the Mongo Client connection based on whether you need authentication or not.
     *
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $username
     * @param string $password
     */
    private function makeConnection(string $host, string $port, string $database, string $username, string $password)
    {
        if (!empty($username) && !empty($password)) {
            return new MongoClient('mongodb://' . $host . ':' . $port, ['user' => $username, 'pwd' => $password]);
        }

        return new MongoClient('mongodb://' . $host . ':' . $port);
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
    public static function getInstance(string $host, string $port, string $database, string $username, string $password): Client
    {
        if (null === static::$instance) {
            static::$instance = new static($host, $port, $database, $username, $password);
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
