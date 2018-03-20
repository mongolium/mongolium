<?php

namespace Tests\Core\Feature;

use Tests\Core\Helper\Post as PostHelper;
use Mongolium\Core\Services\Db\Orm;
use Mongolium\Core\Services\Db\Client;
use Mongolium\Core\Model\Post;
use Tests\Core\FeatureCase;
use Carbon\Carbon;
use ReallySimpleJWT\TokenBuilder;
use ReallySimpleJWT\TokenValidator;
use Mongolium\Core\Services\Token;
use Mockery as m;

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

    public function testGetPostsPublished()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post(false, false));
        $orm->create(Post::class, PostHelper::post(false, false));

        $response = $this->request('GET', '/posts');

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, count($json->data));
    }

    public function testGetPostsPublishedWithFutureDate()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $post = PostHelper::post();
        $post['publish_at'] = Carbon::now()->addMinute()->toDateTimeString();

        $orm->create(Post::class, $post);
        $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post(false, false));

        $response = $this->request('GET', '/posts');

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(1, count($json->data));
    }

    public function testGetPost()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $post = $orm->create(Post::class, PostHelper::post());

        $data = $post->extract();

        $response = $this->request('GET', '/posts/' . $data['id']);

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertEquals($data['title'], $json->data->title);
    }

    public function testCreatePost()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'POST',
            '/posts',
            ['form_params' =>
                [
                    'title' => 'This is a post',
                    'description' => 'This is a sentence about the post',
                    'text' => 'This is the text for the post',
                    'tags' => ['post', 'blog'],
                    'author_id' => '123bcdE',
                    'creator_id' => '123bcdE',
                    'publish' => false,
                    'publish_at' => '2018-03-18 14:12:43'
                ]
            , 'headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertEquals('This is a post', $json->data->title);
        $this->assertEquals('this-is-a-post', $json->data->slug);
    }

    public function testUpdatePost()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $post = $orm->create(Post::class, PostHelper::post());

        $post = $post->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'PATCH',
            '/posts',
            ['form_params' =>
                [
                    'id' => $post['id'],
                    'title' => 'This is an updated post',
                    'description' => 'This is a sentence about the post',
                    'text' => 'This is the text for the post',
                    'tags' => ['post', 'blog'],
                    'author_id' => '123bcdE',
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
        $this->assertEquals('This is an updated post', $json->data->title);
        $this->assertEquals('this-is-an-updated-post', $json->data->slug);
    }

    public function testPostDelete()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $post = $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post());

        $collection = $orm->all(Post::class);

        $this->assertSame(2, $collection->count());

        $post = $post->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'delete',
            '/posts/' . $post['id'],
            ['headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Post deleted.', $json->data->message);

        $collection = $orm->all(Post::class);

        $this->assertSame(1, $collection->count());
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE')));

        $orm->drop(Post::class);
    }
}
