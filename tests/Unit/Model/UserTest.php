<?php

namespace Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Mongolium\Model\User;

class UserTest extends TestCase
{
    public function testUserHydate()
    {
        $user = User::hydrate(
            [
                'id' => '1a',
                'username' => 'rob',
                'password' => 'w',
                'type' => 'admin'
            ]
        );

        $this->assertInstanceOf(User::class, $user);
    }

    public function testGetTable()
    {
        $this->assertEquals('users', User::getTable());
    }

    public function testGetUnique()
    {
        $this->assertEquals('username', User::getUnique()[0]);
    }
}
