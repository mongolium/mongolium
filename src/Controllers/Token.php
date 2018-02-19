<?php

namespace Helium\Controllers;

use Helium\Services\Token as TokenService;
use Helium\Helper\Auth as AuthHelper;
use Helium\Helper\JsonResponse as JsonResponseHelper;
use Helium\Services\JsonResponse;

class Token
{
    use AuthHelper, JsonResponseHelper;

    private $token;

    public function __construct(TokenService $token)
    {
        $this->token = $token;
    }

    public function get($request, $response)
    {
        return $response->withJson(['foo' => 'bar'], 200);
    }

    public function update($request, $response)
    {
        $bearer = $this->getBearerFromAuthorisationHeader($request->getHeaderLine('AUTHORIZATION'));

        return $this->jsonResponse($response,
            new JsonResponse(
                200,
                'OK',
                $this->token->renewToken(
                    $this->getJWTFromBearer($bearer),
                    getenv('TOKEN_SECRET'),
                    getenv('TOKEN_EXPIRY')
                ),
                'token',
                [],
                ['self' => '/token/update']
            )
        );
    }
}
