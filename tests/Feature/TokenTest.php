<?php

namespace Tests\Feature;

use Tests\FeatureCase;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Helium\Services\Token;
use Carbon\Carbon;

class TokenTest extends FeatureCase
{
    public function testUpdateToken()
    {
        $token = new Token(new TokenBuilder, new TokenValidator);

        $jwt = $token->getToken(1, 'user', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request('POST', '/token/update', ['headers' => ['Authorization' => 'Bearer ' . $jwt]]);

        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode($response->getBody());

        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals('token', $json->type);
    }

    // public function testUpdateTokenNoToken()
    // {
    //     $response = $this->request('POST', '/token/update');
    //
    //     $this->assertEquals(400, $response->getStatusCode());
    //
    //     $json = json_decode($response->getBody());
    //
    //     $this->assertTrue(isset($json->error));
    // }
    //
    // public function testUpdateTokenBadToken()
    // {
    //     $response = $this->request('POST', '/token/update', ['token' => '123']);
    //
    //     $this->assertEquals(401, $response->getStatusCode());
    //
    //     $json = json_decode($response->getBody());
    //
    //     $this->assertTrue(isset($json->error));
    // }
}
