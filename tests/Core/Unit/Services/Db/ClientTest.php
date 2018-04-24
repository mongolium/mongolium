<?php

namespace Tests\Core\Unit\Services\Db;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Services\Db\Client;

class ClientTest extends TestCase
{

     public function testClient()
     {
         $client = Client::getInstance('127.0.0.1', 27017, 'unknowndb', 'hello', 'world');

         $this->assertInstanceOf(Client::class, $client);
     }

     public function testGetConnection()
     {
         $client = Client::getInstance('127.0.0.1', 27017, 'unknowndb', 'hello', 'world');

         $this->assertInstanceOf('MongoDB\Client', $client->getConnection());
     }

     public function testGetCollection()
     {
         $client = Client::getInstance('127.0.0.1', 27017, 'unknowndb', 'hello', 'world');

         $this->assertInstanceOf('MongoDB\Collection', $client->getCollection('cars'));
     }
}
