<?php

namespace Tests\Feature;

use Tests\Helper\Post as PostHelper;
use Mongolium\Services\Db\Orm;
use Mongolium\Services\Db\Client;
use Mongolium\Model\Post;
use Tests\FeatureCase;

class PostTest extends FeatureCase
{
    public function testGetPosts()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post());

        $response = $this->request('GET', '/posts');

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertEquals(2, count($json->data));
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Post::class);
    }
}
