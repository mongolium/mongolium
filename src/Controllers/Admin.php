<?php

namespace Mongolium\Controllers;

use Mongolium\Services\Admin as AdminService;
use Mongolium\Helper\Id;
use Mongolium\Services\Response\Response;
use Mongolium\Services\Token;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;
use Throwable;

class Admin
{
    use Id;

    protected $admin;

    public function __construct(AdminService $admin)
    {
        $this->admin = $admin;
    }

    public function create(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $result = $this->admin->create($request->getParsedBody());

            return Response::make()->respond201(
                $response,
                $this->uniqueId(),
                'admin',
                $result->extract(),
                ['self' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond401(
                $response,
                $e->getMessage(),
                ['self' => '/admins', 'token' => '/token']
            );
        }
    }

    public function read(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $results = $this->admin->read($request->getParsedBody());

            $data = [];

            foreach ($results as $row) {
                $data[] = $row->hide();
            }

            return Response::make()->respond200(
                $response,
                $this->uniqueId(),
                'admin',
                $data,
                ['self' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond401(
                $response,
                $e->getMessage(),
                ['self' => '/admins', 'token' => '/token']
            );
        }
    }
}
