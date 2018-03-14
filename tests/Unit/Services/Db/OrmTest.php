<?php

namespace Tests\Unit\Services\Db;

use PHPUnit\Framework\TestCase;
use Mongolium\Services\Db\Orm;
use Mongolium\Model\Admin;
use Tests\Helper\Admin as AdminHelper;
use Mockery as m;
use MongoDB\InsertOneResult;

class OrmTest extends TestCase
{
    public function testFind()
    {
        $admin = AdminHelper::admin(true);

        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('findOneAsArray')->once()->andReturn($admin);

        $result = $orm->find(Admin::class, ['username' => $admin['username']]);

        $this->assertInstanceOf(Admin::class, $result);
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

        $admin = $orm->create(Admin::class, AdminHelper::admin());

        $this->assertInstanceOf(Admin::class, $admin);
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

        $admin = $orm->create(Admin::class, AdminHelper::admin());
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testCreateHasId()
    {
        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('hasId')->once()->andReturn(true);

        $admin = $orm->create(Admin::class, AdminHelper::admin());
    }

    /**
     * @expectedException Mongolium\Exceptions\OrmException
     */
    public function testCreateExists()
    {
        $orm = m::mock(Orm::class)->makePartial();
        $orm->shouldReceive('hasId')->once()->andReturn(false);
        $orm->shouldReceive('exists')->once()->andReturn(true);

        $admin = $orm->create(Admin::class, AdminHelper::admin());
    }
}
