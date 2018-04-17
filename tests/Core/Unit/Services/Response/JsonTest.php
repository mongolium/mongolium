<?php

namespace Tests\Core\Unit\Services\Response;

use Mongolium\Core\Services\Response\Json;
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

        $this->assertSame(404, $json->getCode());
        $this->assertSame('Not Found', $json->getMessage());
        $this->assertSame('123', $json->getId());
        $this->assertSame('error', $json->getType());
        $this->assertSame([], $json->getData());
        $this->assertSame('/token', $json->getLinks()['token']);
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
