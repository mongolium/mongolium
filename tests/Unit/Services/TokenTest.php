<?php

namespace Tests\Services;

use PHPUnit\Framework\TestCase;
use Mongolium\Services\Token as TokenService;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use ReallySimpleJWT\Token;
use Carbon\Carbon;
use Mongolium\Services\Db\Orm;
use Mockery as m;

class TokenTest extends TestCase
{
    public function testTokenService()
    {
        $token = new TokenService(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $this->assertInstanceOf(TokenService::class, $token);
    }

    public function testGetToken()
    {
        $token = new TokenService(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken(2, 'admin', '123456*$abcDEF', 5, 'my business');

        $this->assertTrue(Token::validate($jwt, '123456*$abcDEF'));
    }

    public function testGetPayload()
    {
        $token = new TokenService(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken(1, 'editor', '123456*$abcDEF', 5, 'my business');

        $payload = $token->getPayload($jwt);

        $this->assertEquals($payload->user_id, 1);
        $this->assertEquals($payload->user_type, 'editor');
    }

    public function testRenewToken()
    {
        $token = new TokenService(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken(5, 'user', '123456*$abcDEF', 1, 'my business');

        $newJwt = $token->renewToken($jwt, '123456*$abcDEF', 21);

        $this->assertNotEquals($jwt, $newJwt);

        $this->assertTrue(Token::validate($jwt, '123456*$abcDEF'));
        $this->assertTrue(Token::validate($newJwt, '123456*$abcDEF'));
    }

    public function testValidateToken()
    {
        $token = new TokenService(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = Token::getToken(5, '123456*$abcDEF', Carbon::now()->addMinutes(2)->toDateTimeString(), 'my business');

        $this->assertTrue($token->validateToken($jwt, '123456*$abcDEF'));
    }
}
