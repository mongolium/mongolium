<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Mongolium\Services\Token;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\User;
use Mongolium\Helper\BasicAuth;
use Mockery as m;

class TokenTest extends FeatureCase
{
    use BasicAuth;

    public function setUp()
    {
        parent::setUp();

        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(User::class);

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
    }

    public function testGetToken()
    {
        $response = $this->request('POST', '/token', ['headers' => ['Authorization' => 'Basic ' . $this->encode('rob', 'waller')]]);

        $json = json_decode($response->getBody());

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
    }

    public function testGetTokenNoUsernamePassword()
    {
        $response = $this->request('POST', '/token');

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
        $response = $this->request('POST', '/token', ['headers' => ['Authorization' => 'Basic 123']]);

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

        $jwt = $token->makeToken('1123', 'user', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request('PATCH', '/token', ['headers' => ['Authorization' => 'Bearer ' . $jwt]]);

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody());

        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('token', $json->type);
    }

    public function testUpdateTokenNoToken()
    {
        $response = $this->request('PATCH', '/token');

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
        $response = $this->request('PATCH', '/token', ['headers' => ['Authorization' => 'Bearer 123']]);

        $this->assertEquals(401, $response->getStatusCode());

        $json = json_decode($response->getBody());

        $this->assertTrue(isset($json->errors));
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('401', $json->errors->code);
        $this->assertEquals('Unathorised: Please provide a valid authentication token', $json->errors->message);
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(User::class);
    }
}
