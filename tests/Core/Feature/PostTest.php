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
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post());

        $response = $this->request('GET', '/api/posts');

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertSame(2, count($json->data));
    }

    public function testGetPostsPublished()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post(false, false));
        $orm->create(Post::class, PostHelper::post(false, false));

        $response = $this->request('GET', '/api/posts');

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(1, count($json->data));
    }

    public function testGetPostsPublishedWithFutureDate()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $post = PostHelper::post();
        $post['publish_at'] = Carbon::now()->addMinute()->toDateTimeString();

        $orm->create(Post::class, $post);
        $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post(false, false));

        $response = $this->request('GET', '/api/posts');

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(1, count($json->data));
    }

    public function testGetPost()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $post = $orm->create(Post::class, PostHelper::post());

        $data = $post->extract();

        $response = $this->request('GET', '/api/posts/' . $data['id']);

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertSame($data['title'], $json->data->title);
    }

    public function testCreatePost()
    {
        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'POST',
            '/api/posts',
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

        $this->assertSame(201, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertSame('This is a post', $json->data->title);
        $this->assertSame('this-is-a-post', $json->data->slug);
    }

    public function testUpdatePost()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $post = $orm->create(Post::class, PostHelper::post());

        $post = $post->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'PATCH',
            '/api/posts',
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

        $this->assertSame(200, $response->getStatusCode());
        $this->assertTrue(isset($json->id));
        $this->assertTrue(isset($json->links));
        $this->assertTrue(isset($json->data));
        $this->assertSame('This is an updated post', $json->data->title);
        $this->assertSame('this-is-an-updated-post', $json->data->slug);
    }

    public function testPostDelete()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $post = $orm->create(Post::class, PostHelper::post());
        $orm->create(Post::class, PostHelper::post());

        $collection = $orm->all(Post::class);

        $this->assertSame(2, $collection->count());

        $post = $post->extract();

        $token = new Token(new TokenBuilder, new TokenValidator, m::mock(Orm::class));

        $jwt = $token->makeToken('1abc4', 'admin', getenv('TOKEN_SECRET'), 10, 'test');

        $response = $this->request(
            'delete',
            '/api/posts/' . $post['id'],
            ['headers' => ['Authorization' => 'Bearer ' . $jwt]]
        );

        $json = json_decode($response->getBody());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Post deleted.', $json->data->message);

        $collection = $orm->all(Post::class);

        $this->assertSame(1, $collection->count());
    }

    public function tearDown()
    {
        $orm = new Orm(Client::getInstance(getenv('MONGO_HOST'), getenv('MONGO_PORT'), getenv('MONGO_DATABASE'), getenv('MONGO_USERNAME'), getenv('MONGO_PASSWORD')));

        $orm->drop(Post::class);
    }
}
