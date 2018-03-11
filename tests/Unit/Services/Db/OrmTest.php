<?php

namespace Tests\Unit\Services\Db;

use PHPUnit\Framework\TestCase;
use Mongolium\Services\Db\Orm;
use Mongolium\Model\User;
use Mockery as m;
use MongoDB\InsertOneResult;

class OrmTest extends TestCase
{
    public function testFind()
    {
        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('findOneAsArray')->once()->andReturn(
            [
                'id' => '123',
                'username' => 'rob',
                'password' => 'w',
                'type' => 'admin'
            ]
        );

        $result = $orm->find(User::class, ['username' => 'rob']);

        $this->assertInstanceOf(User::class, $result);
    }

    public function testCreate()
    {
        $result = m::mock(InsertOneResult::class);
        $result->shouldReceive('getInsertedId')->once()->andReturn(1);

        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('hasId')->once()->andReturn(false);
        $orm->shouldReceive('exists')->once()->andReturn(false);
        $orm->shouldReceive('insertEntity')->once()->andReturn(
            $result
        );

        $user = $orm->create(User::class, ['username' => 'rob', 'password' => 'we', 'type' => 'editor']);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testCreateBadReturnData()
    {
        $result = m::mock(InsertOneResult::class);
        $result->shouldReceive('getInsertedId')->once()->andReturn(null);

        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('hasId')->once()->andReturn(false);
        $orm->shouldReceive('exists')->once()->andReturn(false);
        $orm->shouldReceive('insertEntity')->once()->andReturn(
            $result
        );

        $user = $orm->create(User::class, ['username' => 'rob', 'password' => 'we', 'type' => 'editor']);
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testCreateHasId()
    {
        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('hasId')->once()->andReturn(true);

        $user = $orm->create(User::class, ['username' => 'rob', 'password' => 'we', 'type' => 'editor']);
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testCreateExists()
    {
        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('hasId')->once()->andReturn(false);
        $orm->shouldReceive('exists')->once()->andReturn(true);

        $user = $orm->create(User::class, ['username' => 'rob', 'password' => 'we', 'type' => 'editor']);
    }
}
