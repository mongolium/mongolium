<?php

namespace Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Helium\Helper\BasicAuth;

class BasicAuthTest extends TestCase
{
    use BasicAuth;

    public function testDecode()
    {
        $userPassword = base64_encode('hello:world');

        $result = $this->decode($userPassword);

        $this->assertEquals('hello', $result['username']);
        $this->assertEquals('world', $result['password']);
    }

    public function testDecodeOnlyUsername()
    {
        $userPassword = base64_encode('hello:');

        $result = $this->decode($userPassword);

        $this->assertEquals('hello', $result['username']);
        $this->assertEquals('', $result['password']);
    }

    public function testDecodeOnlyPassword()
    {
        $userPassword = base64_encode(':world');

        $result = $this->decode($userPassword);

        $this->assertEquals('', $result['username']);
        $this->assertEquals('world', $result['password']);
    }

    public function testDecodeOnlyColon()
    {
        $userPassword = base64_encode(':');

        $result = $this->decode($userPassword);

        $this->assertEquals('', $result['username']);
        $this->assertEquals('', $result['password']);
    }

    public function testDecodeEmpty()
    {
        $this->assertEquals(0, count($this->decode('')));
    }

    public function testDecodeNoColon()
    {
        $this->assertEquals(0, count($this->decode('123Abc')));
    }
}
