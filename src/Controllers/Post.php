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
            $results = $this->post->getPublished();

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
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    public function readOne(Request $request, SlimResponse $response, array $args): SlimResponse
    {
        try {
            $result = $this->post->getPost($args['id']);

            $post = $result->extract();

            return Response::make()->respond200(
                $response,
                $post['id'],
                'post',
                $post,
                ['self' => '/posts/' . $post['id'], 'posts' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    public function create(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $result = $this->post->create($request->getParsedBody());

            $post = $result->extract();
            $post['link'] = '/posts/' . $post['id'];

            return Response::make()->respond201(
                $response,
                $post['id'],
                'post',
                $post,
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    public function update(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $result = $this->post->update($request->getParsedBody());

            $post = $result->extract();
            $post['link'] = '/posts/' . $post['id'];

            return Response::make()->respond200(
                $response,
                $post['id'],
                'post',
                $post,
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }
}
