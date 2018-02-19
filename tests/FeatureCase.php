<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;

class FeatureCase extends TestCase
{
    private $client;

    protected function setUp()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__ . '/../');
        $dotenv->load();

        $this->client = new Client();
    }

    protected function request(string $type, string $url, array $data = [])
    {
        return $this->client->request($type, 'http://localhost' . $url, $data);
    }

    protected function tearDown()
    {
        $this->client = null;
    }
}
