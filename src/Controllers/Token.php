<?php

namespace Mongolium\Controllers;

use Mongolium\Services\Token as TokenService;
use Mongolium\Helper\Auth as AuthHelper;
use Mongolium\Helper\JsonResponse as JsonResponseHelper;
use Mongolium\Services\JsonResponse;
use Mongolium\Helper\BasicAuth;
use Mongolium\Helper\Environment;
use Mongolium\Helper\Id;
use Throwable;

class Token
{
    use AuthHelper,
    JsonResponseHelper,
    BasicAuth,
    Environment,
    Id;

    private $token;

    public function __construct(TokenService $token)
    {
        $this->token = $token;
    }

    public function create($request, $response)
    {
        $userPassword = $this->decode($request->getAttribute('basic_auth'));

        try {
            $token = $this->token->createToken(
                $userPassword['username'],
                $userPassword['password'],
                $this->env('TOKEN_SECRET'),
                $this->env('TOKEN_EXPIRY'),
                $this->env('TOKEN_ISSUER')
            );
        } catch (Throwable $e) {
            return $this->jsonResponse($response,
                new JsonResponse(
                    401,
                    'Unathorised: ' . $e->getMessage(),
                    $this->generateUniqueId(),
                    'error',
                    [],
                    [
                        'self' => '/token'
                    ]
                )
            );
        }

        return $this->jsonResponse($response,
            new JsonResponse(
                201,
                'OK',
                $token,
                'token',
                [],
                ['self' => '/token']
            )
        );
    }

    public function update($request, $response)
    {
        return $this->jsonResponse($response,
            new JsonResponse(
                200,
                'OK',
                $this->token->renewToken(
                    $request->getAttribute('bearer_token'),
                    getenv('TOKEN_SECRET'),
                    getenv('TOKEN_EXPIRY')
                ),
                'token',
                [],
                ['self' => '/token']
            )
        );
    }
}
