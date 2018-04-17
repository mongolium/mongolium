<?php

namespace Tests\Core\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Helper\Auth;
use Slim\Http\Request;
use Mockery as m;

class AuthTest extends TestCase
{
    use Auth;

    public function testGetAuthorizationAttribute()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('Bearer 123');

        $result = $this->getAuthorizationAttribute($request, 'Bearer');

        $this->assertSame('123', $result);
    }

    public function testGetAuthorizationAttributeNoBearer()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('');

        $result = $this->getAuthorizationAttribute($request, 'Bearer');

        $this->assertSame('', $result);
    }

    public function testGetAuthorizationAttributeBadData()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('123');

        $result = $this->getAuthorizationAttribute($request, 'Bearer');

        $this->assertSame('', $result);
    }

    public function testGetAuthorizationAttributeBadAtrribute()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('Bearer 123');

        $result = $this->getAuthorizationAttribute($request, 'Bearer ');

        $this->assertSame('123', $result);
    }

    public function testGetAuthorizationAttributeWithCommas()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('Bearer 123, Basic 456, Something 789');

        $result = $this->getAuthorizationAttribute($request, 'Bearer ');

        $this->assertSame('123', $result);
    }

    public function testGetAuthorizationAttributeNoHeader()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(false);

        $result = $this->getAuthorizationAttribute($request, 'Bearer');

        $this->assertSame('', $result);
    }

    public function testGetBearerToken()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('Bearer 123');

        $result = $this->getBearerToken($request);

        $this->assertSame('123', $result);
    }

    public function testGetBearerTokenBadHeader()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('Basic 123');

        $result = $this->getBearerToken($request);

        $this->assertSame('', $result);
    }

    public function testGetBasicAuth()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('Basic abc');

        $result = $this->getBasicAuth($request);

        $this->assertSame('abc', $result);
    }

    public function testGetBasicAuthBadHeader()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('hasHeader')->once()->andReturn(true);
        $request->shouldReceive('getHeaderLine')->once()->andReturn('Bearer abc');

        $result = $this->getBasicAuth($request);

        $this->assertSame('', $result);
    }
}
