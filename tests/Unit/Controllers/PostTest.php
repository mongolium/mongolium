<?php

namespace Tests\Unit\Controllers;

use Mongolium\Controllers\Post;
use Mongolium\Services\Post as PostService;
use Mongolium\Model\Post as PostModel;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use PHPUnit\Framework\TestCase;
use Tests\Helper\Post as PostHelper;
use Mockery as m;

class PostTest extends TestCase
{
    public function testPost()
    {
        $postService = m::mock(PostService::class);

        $post = new Post($postService);

        $this->assertInstanceOf(Post::class, $post);
    }

    public function testGetPost()
    {
        $post = PostHelper::post(true);

        $postService = m::mock(PostService::class);
        $postService->shouldReceive('getPost')->once()->andReturn(
            PostModel::hydrate($post)
        );

        $postController = new Post($postService);

        $request = m::mock(Request::class);

        $response = new SlimResponse(200);

        $result = $postController->readOne($request, $response, ['id' => '123abc']);

        $this->assertInstanceOf(SlimResponse::class, $response);

        $this->assertEquals(200, $result->getStatusCode());
        $this->assertStringStartsWith('application/json', $result->getHeaderLine('Content-type'));

        $data = $result->__toString();

        $this->assertRegExp('|"id":"' . $post['id'] . '"|', $data);
        $this->assertRegExp('|"title":"' . $post['title'] . '"|', $data);
        $this->assertRegExp('|"slug":"' . $post['slug'] . '"|', $data);
    }
}
