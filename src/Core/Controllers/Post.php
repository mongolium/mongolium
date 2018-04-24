<?php

namespace Mongolium\Core\Controllers;

use Mongolium\Core\Services\Post as PostService;
use Mongolium\Core\Helper\Id;
use Mongolium\Core\Services\Response\Response;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use Mongolium\Core\Exceptions\OrmException;
use Mongolium\Core\Exceptions\ClientException;

/**
 * Controller class to retrieve posts from mongo
 *
 * @author Rob Waller <rdwaller1984@gmail.com>
 */
class Post
{
    use Id;

    /**
     * @var PostService $post
     */
    protected $post;

    /**
     * @param PostService $post
     */
    public function __construct(PostService $post)
    {
        $this->post = $post;
    }

    /**
     * Get a list of posts
     *
     * @param Request $request
     * @param SlimResponse $response
     * @return SlimResponse
     */
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
        } catch (OrmException $e) {
            return Response::respond200(
                $response,
                $this->uniqueId(),
                'post',
                [],
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (ClientException $e) {
            return Response::respond500(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    /**
     * Get a post
     *
     * @param Request $request
     * @param SlimResponse $response
     * @param array $args
     * @return SlimResponse
     */
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
        } catch (OrmException $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (ClientException $e) {
            return Response::respond500(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    /**
     * Create a new post
     *
     * @param Request $request
     * @param SlimResponse $response
     * @return SlimResponse
     */
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
        } catch (OrmException $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (ClientException $e) {
            return Response::respond500(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    /**
     * Update an existing post
     *
     * @param Request $request
     * @param SlimResponse $response
     * @return SlimResponse
     */
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
        } catch (OrmException $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (ClientException $e) {
            return Response::respond500(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }

    /**
     * Delete an existing post
     *
     * @param Request $request
     * @param SlimResponse $response
     * @param array $args
     * @return SlimResponse
     */
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
        } catch (OrmException $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        } catch (ClientException $e) {
            return Response::respond500(
                $response,
                $e->getMessage(),
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }
}
