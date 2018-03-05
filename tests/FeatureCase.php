<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use Throwable;
use InvalidArgumentException;

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
            return $this->client->request($type, 'http://localhost' . $url, $data);
        }
        catch (InvalidArgumentException $e) {
            $this->fail($e->getMessage());
        }
        catch (Throwable $e) {
            return $e->getResponse();
        }
    }

    protected function tearDown()
    {
        $this->client = null;
    }
}
