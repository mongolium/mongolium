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
            $results = $this->page->read();

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
                ['self' => '/posts', 'token' => '/token']
            );
        }
    }
}
