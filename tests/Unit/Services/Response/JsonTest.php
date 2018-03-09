<?php

namespace Tests\Unit\Services\Response;

use Mongolium\Services\Response\Json;
use PHPUnit\Framework\TestCase;

class JsonTest extends TestCase
{
    public function testJson()
    {
        $json = new Json(404, 'Not Found', '123', 'error', [], ['token' => '/token']);

        $this->assertInstanceOf(Json::class, $json);
    }

    public function testJsonData()
    {
        $json = new Json(404, 'Not Found', '123', 'error', [], ['token' => '/token']);

        $this->assertEquals(404, $json->getCode());
        $this->assertEquals('Not Found', $json->getMessage());
        $this->assertEquals('123', $json->getId());
        $this->assertEquals('error', $json->getType());
        $this->assertEquals([], $json->getData());
        $this->assertEquals('/token', $json->getLinks()['token']);
    }

    public function testJsonSuccess()
    {
        $json = new Json(200, 'Not Found', '123', 'error', [], ['token' => '/token']);

        $this->assertTrue($json->isSuccess());
    }

    public function testJsonSuccessTwo()
    {
        $json = new Json(201, 'Not Found', '123', 'error', [], ['token' => '/token']);

        $this->assertTrue($json->isSuccess());
    }

    public function testJsonSuccessFail()
    {
        $json = new Json(404, 'Not Found', '123', 'error', [], ['token' => '/token']);

        $this->assertFalse($json->isSuccess());
    }
}
