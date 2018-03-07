<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use InvalidArgumentException;
use GuzzleHttp\Exception\ClientException;

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
        try {
            return $this->client->request($type, getenv('TEST_HOST') . getenv('TEST_PORT'), $data);
        }
        catch (InvalidArgumentException $e) {
            $this->fail($e->getMessage());
        }
        catch (ClientException $e) {
            return $e->getResponse();
        }
    }

    protected function tearDown()
    {
        $this->client = null;
    }
}
