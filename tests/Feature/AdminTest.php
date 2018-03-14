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

    public function testGetAllAdmins()
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

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Admin::class);
    }
}
