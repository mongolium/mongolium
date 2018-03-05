<?php

namespace Helium\Controllers;

use Helium\Services\Token as TokenService;
use Helium\Helper\Auth as AuthHelper;
use Helium\Helper\JsonResponse as JsonResponseHelper;
use Helium\Services\JsonResponse;
use Helium\Helper\BasicAuth;
use Helium\Helper\Environment;

class Token
{
    use AuthHelper,
    JsonResponseHelper,
    BasicAuth,
    Environment;

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
        }
        catch (Throwable $e) {
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
