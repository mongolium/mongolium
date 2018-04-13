<?php

namespace Tests\Core\Feature;

use Tests\Core\FeatureCase;
use Tests\Core\Helper\Admin as AdminHelper;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Mongolium\Core\Services\Token;
use Mongolium\Core\Services\Db\Orm;
use Mongolium\Core\Services\Db\Client;
use Mongolium\Core\Model\Admin;
use Mongolium\Core\Helper\BasicAuth;
use Mockery as m;

class TokenTest extends FeatureCase
{
    use BasicAuth;

    public function setUp()
    {
        parent::setUp();

        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->drop(Admin::class);
    }

    public function testGetToken()
    {
        $admin = AdminHelper::admin();
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));
        $result = $orm->create(Admin::class, $admin);

        $response = $this->request('POST', '/api/token', ['headers' => ['Authorization' => 'Basic ' . $this->encode($admin['username'], $admin['password'])]]);

        $json = json_decode($response->getBody());

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
    }

    public function testGetTokenNoUsernamePassword()
    {
        $response = $this->request('POST', '/api/token');

        $json = json_decode($response->getBody());

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->errors));
        $this->assertEquals(
            'Bad Request: Please supply a username and password with your request',
            $json->errors->message
        );
    }

    public function testGetTokenBadUsernamePasswordString()
    {
        $response = $this->request('POST', '/api/token', ['headers' => ['Authorization' => 'Basic 123']]);

        $json = json_decode($response->getBody());

        $this->assertEquals(400, $response->getStatusCode());

        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->errors));
        $this->assertEquals(
            'Bad Request: Please supply a valid username and password with your request',
            $json->errors->message
        );
    }

    public function testUpdateToken()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1123', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request('PATCH', '/api/token', ['headers' => ['Authorization' => 'Bearer ' . $jwt]]);

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody());

        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('token', $json->type);
    }

    public function testUpdateTokenNoToken()
    {
        $response = $this->request('PATCH', '/api/token');

        $this->assertEquals(400, $response->getStatusCode());

        $json = json_decode($response->getBody());

        $this->assertTrue(isset($json->errors));
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('400', $json->errors->code);
        $this->assertEquals('Bad Request: Please provide an authentication token', $json->errors->message);
    }

    public function testUpdateTokenBadToken()
    {
        $response = $this->request('PATCH', '/api/token', ['headers' => ['Authorization' => 'Bearer 123']]);

        $this->assertEquals(401, $response->getStatusCode());

        $json = json_decode($response->getBody());

        $this->assertTrue(isset($json->errors));
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('401', $json->errors->code);
        $this->assertEquals('Unauthorized: Please provide a valid authentication token', $json->errors->message);
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->drop(Admin::class);
    }
}
