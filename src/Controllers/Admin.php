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

    public function update(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $result = $this->admin->update($request->getParsedBody());

            $admin = $result->extract();
            $admin['link'] = '/admins/' . $admin['id'];

            return Response::make()->respond200(
                $response,
                $admin['id'],
                'admin',
                $admin,
                ['self' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/admins', 'token' => '/token']
            );
        }
    }

    public function read(Request $request, SlimResponse $response): SlimResponse
    {
        try {
            $results = $this->admin->all($request->getParsedBody());

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

    public function readOne(Request $request, SlimResponse $response, array $args): SlimResponse
    {
        try {
            $result = $this->admin->getAdmin($args['id']);

            $admin = $result->hide();

            return Response::make()->respond200(
                $response,
                $admin['id'],
                'admin',
                $admin,
                ['self' => '/admins/' . $admin['id'], 'admins' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::make()->respond400(
                $response,
                $e->getMessage(),
                ['self' => '/admins', 'token' => '/token']
            );
        }
    }
}
