<?php

namespace Mongolium\Controllers;

use Mongolium\Services\User as UserService;
use Mongolium\Helper\Id;
use Mongolium\Services\Response\Response;
use Throwable;

class User
{
    use Id;

    protected $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    public function create($request, $response)
    {
        try {
            $result = $this->user->create($request->getParsedBody());

            return Response::make()->respond201(
                $response,
                $this->uniqueId(),
                'user',
                $result->extract(),
                [
                    'self' => '/user',
                    'token' => '/token'
                ]
            );
        } catch (Throwable $e) {
            return Response::make()->respond401(
                $response,
                $e->getMessage(),
                [
                    'self' => '/user',
                    'token' => '/token'
                ]
            );
        }
    }
}
