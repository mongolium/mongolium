<?php

namespace Tests\Core\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Helper\BasicAuth;

class BasicAuthTest extends TestCase
{
    use BasicAuth;

    public function testDecode()
    {
        $userPassword = base64_encode('hello:world');

        $result = $this->decode($userPassword);

        $this->assertSame('hello', $result['username']);
        $this->assertSame('world', $result['password']);
    }

    public function testDecodeOnlyUsername()
    {
        $userPassword = base64_encode('hello:');

        $result = $this->decode($userPassword);

        $this->assertSame('hello', $result['username']);
        $this->assertSame('', $result['password']);
    }

    public function testDecodeOnlyPassword()
    {
        $userPassword = base64_encode(':world');

        $result = $this->decode($userPassword);

        $this->assertSame('', $result['username']);
        $this->assertSame('world', $result['password']);
    }

    public function testDecodeOnlyColon()
    {
        $userPassword = base64_encode(':');

        $result = $this->decode($userPassword);

        $this->assertSame('', $result['username']);
        $this->assertSame('', $result['password']);
    }

    public function testDecodeEmpty()
    {
        $this->assertSame(0, count($this->decode('')));
    }

    public function testDecodeNoColon()
    {
        $this->assertSame(0, count($this->decode('123Abc')));
    }

    public function testEncode()
    {
        $this->assertTrue(is_string($this->encode('rob', 'waller')));
    }

    public function testEncodeDecode()
    {
        $result = $this->decode($this->encode('rob', 'waller'));

        $this->assertSame('rob', $result['username']);
        $this->assertSame('waller', $result['password']);
    }
}
