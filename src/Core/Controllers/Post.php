<?php

namespace Mongolium\Core\Controllers;

use Mongolium\Core\Services\Post as PostService;
use Mongolium\Core\Helper\Id;
use Mongolium\Core\Services\Response\Response;
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

            return Response::respond200(
                $response,
                $this->uniqueId(),
                'post',
                $data,
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
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

            return Response::respond200(
                $response,
                $post['id'],
                'post',
                $post,
                ['self' => '/posts/' . $post['id'], 'posts' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
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

            return Response::respond201(
                $response,
                $post['id'],
                'post',
                $post,
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
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

            return Response::respond200(
                $response,
                $post['id'],
                'post',
                $post,
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    public function delete(Request $request, SlimResponse $response, array $args): SlimResponse
    {
        try {
            $this->post->delete($args['id']);

            return Response::respond200(
                $response,
                $this->uniqueId(),
                'post',
                ['message' => 'Post deleted.'],
                ['posts' => '/posts', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }
}
