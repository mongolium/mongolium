<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\User;
use ReallySimple\Collection;

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
        $orm->create(User::class, ['username' => 'john', 'password' => 'smith', 'type' => 'admin']);

        $this->assertEquals(2, $orm->count(User::class, ['type' => 'admin']));
    }

    public function testCountZero()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $this->assertEquals(0, $orm->count(User::class, ['username' => 'rob']));
    }

    public function testUpdate()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $result = $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);

        $data = $result->extract();

        $query = ['id' => $data['id']];

        $data = ['type' => 'user'];

        $update = $orm->update(User::class, $query, $data);

        $this->assertInstanceOf(User::class, $update);

        $updateData = $update->extract();

        $this->assertEquals('user', $updateData['type']);
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testUpdateBadData()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $result = $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);

        $data = $result->extract();

        $query = ['id' => $data['id']];

        $data = ['foo' => 'bar'];

        $update = $orm->update(User::class, $query, $data);
    }

    public function testUpdateMany()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
        $orm->create(User::class, ['username' => 'john', 'password' => 'smith', 'type' => 'admin']);

        $query = ['type' => 'admin'];

        $data = ['type' => 'user'];

        $update = $orm->updateMany(User::class, $query, $data);

        $this->assertInstanceOf(Collection::class, $update);

        $this->assertEquals(2, $update->count());

        $first = $update->first();

        $this->assertInstanceOf(User::class, $first);

        $this->assertEquals('user', $first->extract()['type']);
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testUpdateManyBadData()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
        $orm->create(User::class, ['username' => 'john', 'password' => 'smith', 'type' => 'admin']);

        $query = ['type' => 'admin'];

        $data = ['foo' => 'bar'];

        $update = $orm->updateMany(User::class, $query, $data);
    }

    public function testAll()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
        $orm->create(User::class, ['username' => 'john', 'password' => 'smith', 'type' => 'user']);

        $collection = $orm->all(User::class);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals(2, $collection->count());

        $this->assertInstanceOf(User::class, $collection->first());
    }

    public function testAllWithQuery()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
        $orm->create(User::class, ['username' => 'john', 'password' => 'smith', 'type' => 'user']);
        $orm->create(User::class, ['username' => 'ada', 'password' => 'jones', 'type' => 'user']);

        $collection = $orm->all(User::class, ['type' => 'user']);

        $this->assertEquals(2, $collection->count());

        $this->assertInstanceOf(User::class, $collection->first());
    }

    public function testDelete()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
        $orm->create(User::class, ['username' => 'john', 'password' => 'smith', 'type' => 'user']);
        $orm->create(User::class, ['username' => 'ada', 'password' => 'jones', 'type' => 'user']);

        $this->assertTrue($orm->delete(User::class, ['username' => 'john']));

        $all = $orm->all(User::class);

        $this->assertEquals(2, $all->count());
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(User::class);
    }
}
