<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use Tests\Helper\Admin as AdminHelper;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Mongolium\Services\Token;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\Admin;
use Mockery as m;

class AdminTest extends FeatureCase
{
    public function testCreateAdmin()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'POST',
            '/admins',
            ['form_params' =>
                [
                    'username' => 'rob',
                    'password' => 'hello',
                    'first_name' => 'rob',
                    'last_name' => 'smith',
                    'email' => 'rob@test.com',
                    'type' => 'admin'
                ]
            , 'headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('admin', $json->type);
    }


    public function testUpdateAdmin()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin = $orm->create(Admin::class, AdminHelper::admin());

        $admin = $admin->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'PATCH',
            '/admins',
            ['form_params' =>
                [
                    'id' => $admin['id'],
                    'username' => 'updaterob',
                    'password' => 'updatehello',
                    'first_name' => 'rob',
                    'last_name' => 'smith',
                    'email' => 'rob@test.com',
                    'type' => 'admin'
                ]
            , 'headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertEquals('updaterob', $json->data->username);
        $this->assertEquals('updatehello', $json->data->password);
    }

    public function testGetAdmins()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Admin::class, AdminHelper::admin());

        $orm->create(Admin::class, AdminHelper::admin());

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'GET',
            '/admins',
            ['headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals(2, count($json->data));
    }

    public function testGetAdmin()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin = $orm->create(Admin::class, AdminHelper::admin());

        $data = $admin->extract();

        $response = $this->request(
            'GET',
            '/admins/' . $data['id'],
            ['headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertEquals($data['id'], $json->data->id);
        $this->assertEquals($data['first_name'], $json->data->first_name);
    }

    public function testAdminDelete()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $admin = $orm->create(Admin::class, AdminHelper::admin());
        $orm->create(Admin::class, AdminHelper::admin());

        $collection = $orm->all(Admin::class);

        $this->assertSame(2, $collection->count());

        $admin = $admin->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'delete',
            '/admins/' . $admin['id'],
            ['headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Admin deleted.', $json->data->message);

        $collection = $orm->all(Admin::class);

        $this->assertSame(1, $collection->count());
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Admin::class);
    }
}
