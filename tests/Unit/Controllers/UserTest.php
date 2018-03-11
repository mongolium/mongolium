<?php

namespace Tests\Unit\Controllers;

use Mongolium\Controllers\User;
use Mongolium\Services\User as UserService;
use Mongolium\Model\User as UserModel;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class UserTest extends TestCase
{
    public function testUser()
    {
        $userService = m::mock(UserService::class);

        $user = new User($userService);

        $this->assertInstanceOf(User::class, $user);
    }

    public function testUserCreate()
    {
        $userService = m::mock(UserService::class);
        $userService->shouldReceive('create')->once()->andReturn(
            UserModel::hydrate(['id' => '1', 'username' => 'rob', 'password' => 'w', 'type' => 'user'])
        );

        $user = new User($userService);

        $request = m::mock(Request::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);

        $response = new SlimResponse(200);

        $result = $user->create($request, $response);

        $this->assertInstanceOf(SlimResponse::class, $result);

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"1"|', $data);
        $this->assertRegExp('|"username":"rob"|', $data);
        $this->assertRegExp('|"password":"w"|', $data);
        $this->assertRegExp('|"type":"user"|', $data);
    }
}
