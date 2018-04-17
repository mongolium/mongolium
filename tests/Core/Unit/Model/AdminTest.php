<?php

namespace Tests\Core\Unit\Model;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Model\Admin;

class AdminTest extends TestCase
{
    public function testAdminHydate()
    {
        $admin = Admin::hydrate(
            [
                'id' => '1a',
                'username' => 'rob',
                'password' => 'smith',
                'email' => 'rob@test.com',
                'first_name' => 'rob',
                'last_name' => 'smith',
                'type' => 'admin',
                'created_at' => '2018-03-13 13:40:02',
                'updated_at' => '2018-03-13 13:40:02'
            ]
        );

        $this->assertInstanceOf(Admin::class, $admin);
    }

    public function testAdminExtract()
    {
        $admin = Admin::hydrate(
            [
                'id' => '1a',
                'username' => 'rob',
                'password' => 'smith',
                'email' => 'rob@test.com',
                'first_name' => 'rob',
                'last_name' => 'smith',
                'type' => 'admin',
                'created_at' => '2018-03-13 13:40:02',
                'updated_at' => '2018-03-13 13:40:02'
            ]
        );

        $data = $admin->extract();

        $this->assertSame(9, count($data));
        $this->assertTrue(isset($data['id']));
        $this->assertTrue(isset($data['username']));
        $this->assertTrue(isset($data['password']));
        $this->assertTrue(isset($data['email']));
        $this->assertTrue(isset($data['first_name']));
        $this->assertTrue(isset($data['last_name']));
        $this->assertTrue(isset($data['type']));
        $this->assertTrue(isset($data['created_at']));
        $this->assertTrue(isset($data['updated_at']));
    }

    public function testGetTable()
    {
        $this->assertSame('admins', Admin::getTable());
    }

    public function testGetUnique()
    {
        $this->assertSame('username', Admin::getUnique()[0]);
    }
}
