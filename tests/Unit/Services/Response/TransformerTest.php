<?php

namespace Tests\Unit\Services\Response;

use Mongolium\Services\Response\Transformer;
use Mongolium\Services\Response\Json;
use PHPUnit\Framework\TestCase;

class TransformerTest extends TestCase
{
    public function testTransformer()
    {
        $json = new Json(404, 'Not Found', '123', 'error', [], ['token' => '/token']);

        $transformer = new Transformer($json);

        $this->assertInstanceOf(Transformer::class, $transformer);
    }

    public function testTransformerError()
    {
        $json = new Json(404, 'Not Found', '123', 'error', [], ['token' => '/token']);

        $transformer = new Transformer($json);

        $data = $transformer->getData();

        $this->assertTrue(isset($data['errors']));
        $this->assertTrue(isset($data['errors']['code']));
        $this->assertTrue(isset($data['errors']['message']));
        $this->assertTrue(isset($data['links']));
        $this->assertTrue(isset($data['id']));
        $this->assertFalse(isset($data['data']));
    }

    public function testTransformerSuccess()
    {
        $json = new Json(200, 'OK', '123', 'token', ['car' => 'park'], ['token' => '/token']);

        $transformer = new Transformer($json);

        $data = $transformer->getData();

        $this->assertTrue(isset($data['data']));
        $this->assertTrue(isset($data['links']));
        $this->assertTrue(isset($data['id']));
        $this->assertFalse(isset($data['errors']));
    }
}
