<?php

namespace Tests\Unit\Services\Db;

use PHPUnit\Framework\TestCase;
use Helium\Services\Db\Orm;
use Helium\Model\User;
use Mockery as m;

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
}
