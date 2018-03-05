<?php

namespace Helium\Controllers;

use Helium\Services\User as UserService;
use Helium\Helper\JsonResponse as JsonResponseHelper;
use Helium\Helper\Id;
use Helium\Services\JsonResponse;
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
