<?php

namespace Mongolium\Core\Controllers;

use Mongolium\Core\Services\Admin as AdminService;
use Mongolium\Core\Helper\Id;
use Mongolium\Core\Services\Response\Response;
use Mongolium\Core\Services\Token;
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

            return Response::respond201(
                $response,
                $this->uniqueId(),
                'admin',
                $result->extract(),
                ['self' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond401(
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

            return Response::respond200(
                $response,
                $admin['id'],
                'admin',
                $admin,
                ['self' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
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

            return Response::respond200(
                $response,
                $this->uniqueId(),
                'admin',
                $data,
                ['self' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond401(
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

            return Response::respond200(
                $response,
                $admin['id'],
                'admin',
                $admin,
                ['self' => '/admins/' . $admin['id'], 'admins' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/admins', 'token' => '/token']
            );
        }
    }

    public function delete(Request $request, SlimResponse $response, array $args): SlimResponse
    {
        try {
            $this->admin->delete($args['id']);

            return Response::respond200(
                $response,
                $this->uniqueId(),
                'admin',
                ['message' => 'Admin deleted.'],
                ['admins' => '/admins', 'token' => '/token']
            );
        } catch (Throwable $e) {
            return Response::respond400(
                $response,
                $e->getMessage(),
                ['self' => '/admins', 'token' => '/token']
            );
        }
    }
}
