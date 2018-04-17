<?php

namespace Tests\Core\Feature;

use Tests\Core\Helper\Page as PageHelper;
use Mongolium\Core\Services\Db\Orm;
use Mongolium\Core\Services\Db\Client;
use Mongolium\Core\Model\Page;
use Tests\Core\FeatureCase;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Mongolium\Core\Services\Token;
use Mockery as m;

class PageTest extends FeatureCase
{
    public function testGetPages()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());

        $response = $this->request('GET', '/api/pages');

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertSame(4, count($json->data));
    }

    public function testGetPagesPublished()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page(false, false));
        $orm->create(Page::class, PageHelper::page());

        $response = $this->request('GET', '/api/pages');

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(2, count($json->data));
    }

    public function testGetPage()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $page = $orm->create(Page::class, PageHelper::page());

        $data = $page->extract();

        $response = $this->request('GET', '/api/pages/' . $data['id']);

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertSame($data['title'], $json->data->title);
    }

    public function testCreatePage()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'POST',
            '/api/pages',
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

        $this->assertSame(201, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertSame('This is a page', $json->data->title);
        $this->assertSame('this-is-a-page', $json->data->slug);
    }

    public function testUpdatePage()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $page = $orm->create(Page::class, PageHelper::page());

        $page = $page->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'PATCH',
            '/api/pages',
            ['form_params' =>
                [
                    'id' => $page['id'],
                    'title' => 'This is an updated page',
                    'description' => 'This is a sentence about the page',
                    'text' => 'This is the text for the page',
                    'tags' => ['page', 'home'],
                    'creator_id' => '123bcdE',
                    'publish' => false
                ]
            , 'headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertSame('This is an updated page', $json->data->title);
        $this->assertSame('this-is-an-updated-page', $json->data->slug);
    }

    public function testPageDelete()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $page = $orm->create(Page::class, PageHelper::page());
        $orm->create(Page::class, PageHelper::page());

        $collection = $orm->all(Page::class);

        $this->assertSame(2, $collection->count());

        $page = $page->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'delete',
            '/api/pages/' . $page['id'],
            ['headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Page deleted.', $json->data->message);

        $collection = $orm->all(Page::class);

        $this->assertSame(1, $collection->count());
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->drop(Page::class);
    }
}
