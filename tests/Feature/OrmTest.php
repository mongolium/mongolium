<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use Helium\Services\Db\Orm;
use Helium\Services\Db\Client;
use Helium\Model\User;

class OrmTest extends FeatureCase
{
    public function setUp()
    {
        parent::setUp();

        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(User::class);
    }

    public function testCreate()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $result = $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);

        $this->assertInstanceOf(User::class, $result);

        $data = $result->extract();

        $this->assertEquals('rob', $data['username']);
        $this->assertEquals('waller', $data['password']);
        $this->assertEquals('admin', $data['type']);
        $this->assertTrue(isset($data['id']));
    }

    public function testFind()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);

        $result = $orm->find(User::class, ['username' => 'rob', 'password' => 'waller']);

        $this->assertInstanceOf(User::class, $result);

        $data = $result->extract();

        $this->assertEquals('rob', $data['username']);
        $this->assertEquals('waller', $data['password']);
        $this->assertEquals('admin', $data['type']);
        $this->assertTrue(isset($data['id']));
    }

    public function testCount()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);

        $this->assertEquals(1, $orm->count(User::class, ['username' => 'rob']));
    }

    public function testCountMultiple()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
        $orm->create(User::class, ['username' => 'john', 'password' => 'galt', 'type' => 'admin']);

        $this->assertEquals(2, $orm->count(User::class, ['type' => 'admin']));
    }

    public function testCountZero()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $this->assertEquals(0, $orm->count(User::class, ['username' => 'rob']));
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(User::class);
    }
}
