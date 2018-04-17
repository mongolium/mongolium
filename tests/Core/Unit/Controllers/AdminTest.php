<?php

namespace Tests\Core\Unit\Controllers;

use Mongolium\Core\Controllers\Admin;
use Mongolium\Core\Services\Admin as AdminService;
use Mongolium\Core\Model\Admin as AdminModel;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;
use Tests\Core\Helper\Admin as AdminHelper;
use ReallySimple\Collection;
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

        $this->assertSame(201, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"' . $admin['id'] . '"|', $data);
        $this->assertRegExp('|"username":"' . $admin['username'] . '"|', $data);
        $this->assertRegExp('|"type":"' . $admin['type'] . '"|', $data);
    }

    public function testAdminUpdate()
    {
        $admin = AdminHelper::admin(true);

        $adminService = m::mock(AdminService::class);
        $adminService->shouldReceive('update')->once()->andReturn(
            AdminModel::hydrate($admin)
        );

        $adminController = new Admin($adminService);

        $request = m::mock(Request::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);

        $response = new SlimResponse(200);

        $result = $adminController->update($request, $response);

        $this->assertInstanceOf(SlimResponse::class, $result);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"' . $admin['id'] . '"|', $data);
        $this->assertRegExp('|"username":"' . $admin['username'] . '"|', $data);
        $this->assertRegExp('|"type":"' . $admin['type'] . '"|', $data);
    }

    public function testAdminRead()
    {
        $admins[] = AdminModel::hydrate(AdminHelper::admin(true));
        $admins[] = AdminModel::hydrate(AdminHelper::admin(true));
        $admins[] = AdminModel::hydrate(AdminHelper::admin(true));
        $admins[] = AdminModel::hydrate(AdminHelper::admin(true));

        $adminService = m::mock(AdminService::class);
        $adminService->shouldReceive('all')->once()->andReturn(
            new Collection($admins)
        );

        $adminController = new Admin($adminService);

        $request = m::mock(Request::class);
        $request->shouldReceive('getParsedBody')->once()->andReturn([]);

        $response = new SlimResponse(200);

        $result = $adminController->read($request, $response);

        $this->assertInstanceOf(SlimResponse::class, $result);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"|', $data);
        $this->assertRegExp('|"type":"admin"|', $data);
        $this->assertRegExp('|"data":\[|', $data);
        $this->assertRegExp('|"links":{|', $data);
    }

    public function testAdminReadOne()
    {
        $admin = AdminHelper::admin(true);

        $adminService = m::mock(AdminService::class);
        $adminService->shouldReceive('getAdmin')->once()->andReturn(
            AdminModel::hydrate($admin)
        );

        $adminController = new Admin($adminService);

        $request = m::mock(Request::class);

        $response = new SlimResponse(200);

        $args = ['id' => 1];

        $result = $adminController->readOne($request, $response, $args);

        $this->assertInstanceOf(SlimResponse::class, $result);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"' . $admin['id'] . '"|', $data);
        $this->assertRegExp('|"username":"' . $admin['username'] . '"|', $data);
        $this->assertRegExp('|"type":"' . $admin['type'] . '"|', $data);
        $this->assertRegExp('|"data":{"|', $data);
    }

    public function testAdminDelete()
    {
        $admin = AdminHelper::admin(true);

        $adminService = m::mock(AdminService::class);
        $adminService->shouldReceive('delete')->once()->andReturn(
            true
        );

        $adminController = new Admin($adminService);

        $request = m::mock(Request::class);

        $response = new SlimResponse(200);

        $args = ['id' => 1];

        $result = $adminController->delete($request, $response, $args);

        $this->assertInstanceOf(SlimResponse::class, $result);

        $this->assertSame(200, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"|', $data);
        $this->assertRegExp('|"message":"Admin deleted\."|', $data);
        $this->assertRegExp('|"data":{"|', $data);
    }
}
