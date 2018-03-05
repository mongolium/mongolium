<?php

namespace Mongolium\Controllers;

use Mongolium\Services\User as UserService;
use Mongolium\Helper\JsonResponse as JsonResponseHelper;
use Mongolium\Helper\Id;
use Mongolium\Services\JsonResponse;
use Throwable;

class User
{
    use JsonResponseHelper, Id;

    protected $user;

    public function __construct(UserService $user)
    {
        $this->user = $user;
    }

    public function create($request, $response)
    {
        try {
            $result = $this->user->create($request->getParsedBody());

            return $this->jsonResponse($response,
                new JsonResponse(
                    201,
                    'OK',
                    $this->generateUniqueId(),
                    'user',
                    $result->extract(),
                    [
                        'self' => '/user',
                        'token' => '/token'
                    ]
                )
            );
        }
        catch (Throwable $e) {
            return $this->jsonResponse($response,
                new JsonResponse(
                    400,
                    'Bad Request: ' . $e->getMessage(),
                    $this->generateUniqueId(),
                    'error',
                    [],
                    [
                        'self' => '/user',
                        'token' => '/token'
                    ]
                )
            );
        }
    }
}
