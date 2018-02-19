<?php

namespace Helium\Middleware;

use Helium\Helper\Auth as AuthHelper;
use ReallySimpleJWT\Token;
use Throwable;

class Auth
{
    use AuthHelper;

    public function __invoke($request, $response, $next)
    {
        try {
            if (!$request->hasHeader('AUTHORIZATION')) {
                return $response->withJson(['error' => 'No Authroization Header supplied with request']);
            }

            $bearer = $this->getBearerFromAuthorisationHeader($request->getHeaderLine('AUTHORIZATION'));

            Token::validate($this->getJWTFromBearer($bearer), getenv('TOKEN_SECRET'));
        }
        catch (Throwable $e) {
            return $response->withJson(['error' => $e->getMessage()]);
        }

        $response = $next($request, $response);

        return $response;
    }
}
