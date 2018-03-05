<?php

namespace Mongolium\Middleware;

use Mongolium\Helper\Auth as AuthHelper;
use ReallySimpleJWT\Token;
use Throwable;
use Mongolium\Helper\JsonResponse as JsonResponseHelper;
use Mongolium\Services\JsonResponse;
use Mongolium\Helper\Id;

class Auth
{
    use AuthHelper, JsonResponseHelper, Id;

    public function __invoke($request, $response, $next)
    {
        try {
            if (empty($request->getAttribute('bearer_token'))) {
                return $this->jsonResponse(
                    $response,
                    new JsonResponse(
                        400,
                        'Bad Request: Please provide an authentication token',
                        $this->generateUniqueId(),
                        'error',
                        [],
                        ['token' => '/token']
                    )
                );
            }

            Token::validate($request->getAttribute('bearer_token'), getenv('TOKEN_SECRET'));
        }
        catch (Throwable $e) {
            return $this->jsonResponse(
                $response,
                new JsonResponse(
                    401,
                    'Unathorised: Please provide a valid authentication token',
                    $this->generateUniqueId(),
                    'error',
                    [],
                    ['token' => '/token']
                )
            );
        }

        return $next($request, $response);
    }
}
