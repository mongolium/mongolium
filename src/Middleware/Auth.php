<?php

namespace Helium\Middleware;

use Helium\Helper\Auth as AuthHelper;
use ReallySimpleJWT\Token;
use Throwable;
use Helium\Helper\JsonResponse as JsonResponseHelper;
use Helium\Services\JsonResponse;
use Helium\Helper\Id;

class Auth
{
    use AuthHelper, JsonResponseHelper, Id;

    public function __invoke($request, $response, $next)
    {
        try {
            if (!$request->hasHeader('AUTHORIZATION')) {
                return $this->jsonResponse(
                    $response,
                    new JsonResponse(
                        400,
                        'Bad Request: Please provide a valid authentication token',
                        $this->generateUniqueId(),
                        'error',
                        [],
                        ['token' => '/token']
                    )
                );
            }

            $bearer = $this->getBearerFromAuthorisationHeader($request->getHeaderLine('AUTHORIZATION'));

            Token::validate($this->getJWTFromBearer($bearer), getenv('TOKEN_SECRET'));
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

        $response = $next($request, $response);

        return $response;
    }
}
