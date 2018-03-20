<?php

namespace Mongolium\Core\Controllers;

use Mongolium\Core\Services\Token as TokenService;
use Mongolium\Core\Helper\Auth as AuthHelper;
use Mongolium\Core\Helper\BasicAuth;
use Mongolium\Core\Helper\Environment;
use Mongolium\Core\Services\Response\Response;
use Throwable;

class Token
{
    use AuthHelper,
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
        } catch (Throwable $e) {
            return Response::make()->respond401(
                $response,
                $e->getMessage(),
                ['self' => '/token']
            );
        }

        return Response::make()->respond201(
                $response,
                $token,
                'token',
                [],
                ['self' => '/token']
        );
    }

    public function update($request, $response)
    {
        return Response::make()->respond200(
            $response,
            $this->token->renewToken(
                $request->getAttribute('bearer_token'),
                getenv('TOKEN_SECRET'),
                getenv('TOKEN_EXPIRY')
            ),
            'token',
            [],
            ['self' => '/token']
        );
    }
}
