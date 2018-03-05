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

        $jwt = $token->makeToken(1, 'user', getenv('TOKEN_SECRET'), 10, 'test');

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

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(User::class);
    }

}
