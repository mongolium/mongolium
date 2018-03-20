<?php

namespace Tests\Unit\Controllers;

use Mongolium\Controllers\Admin;
use Mongolium\Services\Admin as AdminService;
use Mongolium\Model\Admin as AdminModel;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;
use Tests\Helper\Admin as AdminHelper;
use Mockery as m;

class AdminTest extends TestCase
{
    public function testAdmin()
    {
        $adminService = m::mock(AdminService::class);

        $admin = new Admin($adminService);

        $this->assertInstanceOf(Admin::class, $admin);
    }

    public function testAdminCreate()
    {
        $admin = AdminHelper::admin(true);

        $adminService = m::mock(AdminService::class);
        $adminService->shouldReceive('create')->once()->andReturn(
            AdminModel::hydrate($admin)
        );

        $adminController = new Admin($adminService);

        $request = m::mock(Request::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);

        $response = new SlimResponse(200);

        $result = $adminController->create($request, $response);

        $this->assertInstanceOf(SlimResponse::class, $result);

        $this->assertEquals(201, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"' . $admin['id'] . '"|', $data);
        $this->assertRegExp('|"username":"' . $admin['username'] . '"|', $data);
        $this->assertRegExp('|"type":"' . $admin['type'] . '"|', $data);
    }
}
