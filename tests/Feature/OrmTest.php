<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use Tests\Helper\Admin as AdminHelper;
use Tests\Helper\Post as PostHelper;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\Admin;
use Mongolium\Model\Post;
use ReallySimple\Collection;

class OrmTest extends FeatureCase
{
    public function setUp()
    {
        parent::setUp();

        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Admin::class);
        $orm->drop(Post::class);
    }

    public function testCreate()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin = AdminHelper::admin();

        $result = $orm->create(Admin::class, $admin);

        $this->assertInstanceOf(Admin::class, $result);

        $data = $result->extract();

        $this->assertEquals($admin['username'], $data['username']);
        $this->assertEquals($admin['password'], $data['password']);
        $this->assertEquals($admin['type'], $data['type']);
        $this->assertTrue(isset($data['id']));
    }

    public function testFind()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin = AdminHelper::admin();

        $orm->create(Admin::class, $admin);

        $result = $orm->find(Admin::class, ['username' => $admin['username'], 'password' => $admin['password']]);

        $this->assertInstanceOf(Admin::class, $result);

        $data = $result->extract();

        $this->assertEquals($admin['username'], $data['username']);
        $this->assertEquals($admin['password'], $data['password']);
        $this->assertEquals($admin['type'], $data['type']);
        $this->assertTrue(isset($data['id']));
    }

    public function testCount()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin = AdminHelper::admin();

        $orm->create(Admin::class, $admin);

        $this->assertEquals(1, $orm->count(Admin::class, ['username' => $admin['username']]));
    }

    public function testCountMultiple()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin1 = AdminHelper::admin();
        $admin1['type'] = 'admin';

        $admin2 = AdminHelper::admin();
        $admin2['type'] = 'admin';

        $orm->create(Admin::class, $admin1);
        $orm->create(Admin::class, $admin2);

        $this->assertEquals(2, $orm->count(Admin::class, ['type' => 'admin']));
    }

    public function testCountZero()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $this->assertEquals(0, $orm->count(Admin::class, ['username' => 'rob']));
    }

    public function testUpdate()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $result = $orm->create(Admin::class, AdminHelper::admin());

        $data = $result->extract();

        $query = ['id' => $data['id']];

        $data = ['type' => 'user'];

        $update = $orm->update(Admin::class, $query, $data);

        $this->assertInstanceOf(Admin::class, $update);

        $updateData = $update->extract();

        $this->assertEquals('user', $updateData['type']);
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testUpdateBadData()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $result = $orm->create(Admin::class, AdminHelper::admin());

        $data = $result->extract();

        $query = ['id' => $data['id']];

        $data = ['foo' => 'bar'];

        $update = $orm->update(Admin::class, $query, $data);
    }

    public function testUpdateMany()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin1 = AdminHelper::admin();
        $admin1['type'] = 'admin';

        $admin2 = AdminHelper::admin();
        $admin2['type'] = 'super_admin';

        $admin3 = AdminHelper::admin();
        $admin3['type'] = 'admin';

        $orm->create(Admin::class, $admin1);
        $orm->create(Admin::class, $admin2);
        $orm->create(Admin::class, $admin3);

        $query = ['type' => 'admin'];

        $data = ['type' => 'editor'];

        $update = $orm->updateMany(Admin::class, $query, $data);

        $this->assertInstanceOf(Collection::class, $update);

        $this->assertEquals(2, $update->count());

        $first = $update->first();

        $this->assertInstanceOf(Admin::class, $first);

        $this->assertEquals('editor', $first->extract()['type']);
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testUpdateManyBadData()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Admin::class, AdminHelper::admin());

        $orm->create(Admin::class, AdminHelper::admin());

        $query = ['type' => 'admin'];

        $data = ['foo' => 'bar'];

        $update = $orm->updateMany(Admin::class, $query, $data);
    }

    public function testAll()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Admin::class, AdminHelper::admin());

        $orm->create(Admin::class, AdminHelper::admin());

        $collection = $orm->all(Admin::class);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals(2, $collection->count());

        $this->assertInstanceOf(Admin::class, $collection->first());
    }

    public function testAllWithQuery()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin1 = AdminHelper::admin();
        $admin1['type'] = 'editor';

        $admin2 = AdminHelper::admin();
        $admin2['type'] = 'super_admin';

        $admin3 = AdminHelper::admin();
        $admin3['type'] = 'editor';

        $orm->create(Admin::class, $admin1);
        $orm->create(Admin::class, $admin2);
        $orm->create(Admin::class, $admin3);

        $collection = $orm->all(Admin::class, ['type' => 'editor']);

        $this->assertEquals(2, $collection->count());

        $this->assertInstanceOf(Admin::class, $collection->first());
    }

    public function testAllPost()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Post::class, PostHelper::post());

        $orm->create(Post::class, PostHelper::post());

        $collection = $orm->all(Post::class);

        $this->assertInstanceOf(Collection::class, $collection);

        $this->assertEquals(2, $collection->count());

        $this->assertInstanceOf(Post::class, $collection->first());
    }

    public function testDelete()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Admin::class, AdminHelper::admin());

        $admin = AdminHelper::admin();
        $admin['username'] = 'john';

        $orm->create(Admin::class, $admin);

        $orm->create(Admin::class, AdminHelper::admin());

        $this->assertTrue($orm->delete(Admin::class, ['username' => 'john']));

        $all = $orm->all(Admin::class);

        $this->assertEquals(2, $all->count());
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Admin::class);
        $orm->drop(Post::class);
    }
}
