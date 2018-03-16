<?php

namespace Mongolium\Controllers;

use Mongolium\Services\Post as PostService;
use Mongolium\Helper\Id;
use Mongolium\Services\Response\Response;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use Throwable;

class Post
{
    use Id;

    protected $post;

    public function __construct(PostService $post)
    {
        $this->post = $post;
    }

    public function read(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $results = $this->post->read();

            $data = [];

            foreach ($results as $row) {
                $data[] = $row->extract();
            }

            return Response::make()->respond200(
                $response,
                $this->uniqueId(),
                'post',
                $data,
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond401(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }
}
