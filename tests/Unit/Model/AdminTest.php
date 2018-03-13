<?php

namespace Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Mongolium\Model\Admin;

class AdminTest extends TestCase
{
    public function testAdminHydate()
    {
        $admin = Admin::hydrate(
            [
                'id' => '1a',
                'username' => 'rob',
                'password' => 'w',
                'type' => 'admin'
            ]
        );

        $this->assertInstanceOf(Admin::class, $admin);
    }

    public function testGetTable()
    {
        $this->assertEquals('admins', Admin::getTable());
    }

    public function testGetUnique()
    {
        $this->assertEquals('username', Admin::getUnique()[0]);
    }
}
