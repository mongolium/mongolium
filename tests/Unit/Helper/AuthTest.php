<?php

namespace Tests\Helper;

use PHPUnit\Framework\TestCase;
use Helium\Helper\Auth;

class AuthTest extends TestCase
{
    use Auth;

    public function testGetBearerFromAuthorisationHeader()
    {
        $headerString = 'Bearer 123';

        $bearer = $this->getBearerFromAuthorisationHeader($headerString);

        $this->assertEquals('Bearer 123', $bearer);
    }

    public function testGetBearerFromAuthorisationHeaderMultipleHeader()
    {
        $headerString = 'Bearer 123, Password Hello';

        $bearer = $this->getBearerFromAuthorisationHeader($headerString);

        $this->assertEquals('Bearer 123', $bearer);
    }

    public function testGetBearerFromAuthorisationHeaderJumbledHeader()
    {
        $headerString = 'Username MeOneTwoThree, Bearer 123, Password Hello';

        $bearer = $this->getBearerFromAuthorisationHeader($headerString);

        $this->assertEquals('Bearer 123', $bearer);
    }

    /**
     * @expectedException Helium\Exceptions\Auth
     * @expectedExceptionMessage No Bearer found in Authorization Header
     */
    public function testGetBearerFromAuthorisationHeaderNoHeader()
    {
        $headerString = '';

        $this->getBearerFromAuthorisationHeader($headerString);
    }

    public function testGetJWTFromBearer()
    {
        $bearerString = 'Bearer 123';

        $jwt = $this->getJWTFromBearer($bearerString);

        $this->assertEquals(123, $jwt);
    }

    /**
     * @expectedException Helium\Exceptions\Auth
     * @expectedExceptionMessage Authorization Header Bearer empty, please include a JSON Web Token
     */
    public function testGetJWTFromBearerNoJwt()
    {
        $headerString = 'Bearer';

        $this->getJWTFromBearer($headerString);
    }

    /**
     * @expectedException Helium\Exceptions\Auth
     * @expectedExceptionMessage Authorization Header Bearer empty, please include a JSON Web Token
     */
    public function testGetJWTFromBearerNoJwtTwo()
    {
        $headerString = '';

        $this->getJWTFromBearer($headerString);
    }

    /**
     * @expectedException Helium\Exceptions\Auth
     * @expectedExceptionMessage Authorization Header Bearer empty, please include a JSON Web Token
     */
    public function testGetJWTFromBearerNoJwtThree()
    {
        $headerString = 'Bearer123';

        $this->getJWTFromBearer($headerString);
    }
}
