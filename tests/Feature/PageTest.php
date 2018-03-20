<?php

namespace Tests\Feature;

use Tests\Helper\Page as PageHelper;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\Page;
use Tests\FeatureCase;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Mongolium\Services\Token;
use Mockery as m;

class PageTest extends FeatureCase
{
    public function testGetPages()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());

        $response = $this->request('GET', '/pages');

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals(4, count($json->data));
    }

    public function testGetPagesPublished()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page(false, false));
        $orm->create(Page::class, PageHelper::page());

        $response = $this->request('GET', '/pages');

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, count($json->data));
    }

    public function testGetPage()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $page = $orm->create(Page::class, PageHelper::page());

        $data = $page->extract();

        $response = $this->request('GET', '/pages/' . $data['id']);

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertEquals($data['title'], $json->data->title);
    }

    public function testCreatePage()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'POST',
            '/pages',
            ['form_params' =>
                [
                    'title' => 'This is a page',
                    'description' => 'This is a sentence about the page',
                    'text' => 'This is the text for the page',
                    'tags' => ['page', 'about'],
                    'creator_id' => '123bcdE',
                    'publish' => false
                ]
            , 'headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertEquals('This is a page', $json->data->title);
        $this->assertEquals('this-is-a-page', $json->data->slug);
    }

    public function testUpdatePage()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $page = $orm->create(Page::class, PageHelper::page());

        $page = $page->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'PATCH',
            '/pages',
            ['form_params' =>
                [
                    'id' => $page['id'],
                    'title' => 'This is an updated page',
                    'description' => 'This is a sentence about the page',
                    'text' => 'This is the text for the page',
                    'tags' => ['page', 'home'],
                    'creator_id' => '123bcdE',
                    'publish' => false,
                    'publish_at' => '2018-03-18 14:12:43'
                ]
            , 'headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertEquals('This is an updated page', $json->data->title);
        $this->assertEquals('this-is-an-updated-page', $json->data->slug);
    }

    public function testPageDelete()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $page = $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());

        $collection = $orm->all(Page::class);

        $this->assertSame(2, $collection->count());

        $page = $page->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'delete',
            '/pages/' . $page['id'],
            ['headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Page deleted.', $json->data->message);

        $collection = $orm->all(Page::class);

        $this->assertSame(1, $collection->count());
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Page::class);
    }
}
