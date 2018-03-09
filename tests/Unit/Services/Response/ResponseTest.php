<?php

namespace Tests\Unit\Services\Response;

use Mongolium\Services\Response\Response;
use Slim\Http\Response as SlimResponse;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class ResponseTest extends TestCase
{
    public function testResponse()
    {
        $response = new Response;

        $this->assertInstanceOf(Response::class, $response);
    }

    public function test200()
    {
        $response = new Response;

        $slimResponse = new SlimResponse(200);

        $response = $response->respond200($slimResponse, '123', 'token', ['hello' => 'world'], ['/token']);

        $this->assertInstanceOf(
            SlimResponse::class,
            $response
        );

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringStartsWith('application/json', $response->getHeaderLine('Content-type'));
    }

    public function test201()
    {
        $response = new Response;

        $slimResponse = new SlimResponse(200);

        $response = $response->respond201($slimResponse, '123', 'token', ['foo' => 'bar'], ['/token']);

        $this->assertInstanceOf(
            SlimResponse::class,
            $response
        );

        $this->assertEquals(201, $response->getStatusCode());
    }

    public function test400()
    {
        $response = new Response;

        $slimResponse = new SlimResponse(200);

        $response = $response->respond400($slimResponse, 'message', ['/token']);

        $this->assertInstanceOf(
            SlimResponse::class,
            $response
        );

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertStringStartsWith('application/json', $response->getHeaderLine('Content-type'));
    }

    public function test401()
    {
        $response = new Response;

        $slimResponse = new SlimResponse(200);

        $response = $response->respond401($slimResponse, 'message', ['/token']);

        $this->assertInstanceOf(
            SlimResponse::class,
            $response
        );

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function test404()
    {
        $response = new Response;

        $slimResponse = new SlimResponse(200);

        $response = $response->respond404($slimResponse, 'message', ['/token']);

        $this->assertInstanceOf(
            SlimResponse::class,
            $response
        );

        $this->assertEquals(404, $response->getStatusCode());
    }
}
