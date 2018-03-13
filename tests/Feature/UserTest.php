<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Mongolium\Services\Token;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\User;
use Mockery as m;

class UserTest extends FeatureCase
{
    public function testCreateUser()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'user', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'POST',
            '/user',
            ['form_params' => ['username' => 'rob', 'password' => 'hello', 'type' => 'admin'], 'headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('user', $json->type);
    }

    public function testGetAllUsers()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(User::class, ['username' => 'rob', 'password' => 'waller', 'type' => 'admin']);
        $orm->create(User::class, ['username' => 'john', 'password' => 'smith', 'type' => 'admin']);

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'user', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'GET',
            '/user',
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

        $orm->drop(User::class);
    }
}
