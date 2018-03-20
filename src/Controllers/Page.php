<?php

namespace Mongolium\Controllers;

use Mongolium\Services\Page as PageService;
use Slim\Http\Request;
use Slim\Http\Response as SlimResponse;
use Mongolium\Services\Response\Response;
use Mongolium\Helper\Id;
use Throwable;

class Page
{
    use Id;

    protected $page;

    public function __construct(PageService $page)
    {
        $this->page = $page;
    }

    public function read(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $results = $this->page->getPublished();

            $data = [];

            foreach ($results as $row) {
                $data[] = $row->extract();
            }

            return Response::make()->respond200(
                $response,
                $this->uniqueId(),
                'page',
                $data,
                ['self' => '/pages', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond401(
                $response,
                $e->getMessage(),
                ['self' => '/pages', 'token' => '/token']
            );
        }
    }

    public function readOne(Request $request, SlimResponse $response, array $args): SlimResponse
    {
        try {
            $result = $this->page->getPage($args['id']);

            $page = $result->extract();

            return Response::make()->respond200(
                $response,
                $page['id'],
                'page',
                $page,
                ['self' => '/pages/' . $page['id'], 'pages' => '/pages', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/page', 'token' => '/token']
            );
        }
    }

    public function create(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $result = $this->page->create($request->getParsedBody());

            $page = $result->extract();
            $page['link'] = '/pages/' . $page['id'];

            return Response::make()->respond201(
                $response,
                $page['id'],
                'page',
                $page,
                ['self' => '/pages', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/pages', 'token' => '/token']
            );
        }
    }

    public function update(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $result = $this->page->update($request->getParsedBody());

            $page = $result->extract();
            $page['link'] = '/pages/' . $page['id'];

            return Response::make()->respond200(
                $response,
                $page['id'],
                'page',
                $page,
                ['self' => '/pages', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/pages', 'token' => '/token']
            );
        }
    }

    public function delete(Request $request, SlimResponse $response, array $args): SlimResponse
    {
        try {
            $this->page->delete($args['id']);

            return Response::make()->respond200(
                $response,
                $this->uniqueId(),
                'page',
                ['message' => 'Page deleted.'],
                ['pages' => '/pages', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/page', 'token' => '/token']
            );
        }
    }
}
