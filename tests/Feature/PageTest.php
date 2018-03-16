<?php

namespace Tests\Feature;

use Tests\Helper\Page as PageHelper;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\Page;
use Tests\FeatureCase;

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

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Page::class);
    }
}
