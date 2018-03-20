<?php

namespace Mongolium\Core\Middleware;

use Mongolium\Core\Helper\Auth as AuthHelper;
use ReallySimpleJWT\Token;
use Throwable;
use Mongolium\Core\Services\Response\Response;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;

class Auth
{
    use AuthHelper;

    public function __invoke(Request $request, SlimResponse $response, $next): SlimResponse
    {
        try {
            if (empty($request->getAttribute('bearer_token'))) {
                return Response::make()->respond400(
                    $response,
                    'Please provide an authentication token',
                    ['token' => '/token']
                );
            }

            Token::validate($request->getAttribute('bearer_token'), getenv('TOKEN_SECRET'));
        } catch (Throwable $e) {
            return Response::make()->respond401(
                $response,
                'Please provide a valid authentication token',
                ['token' => '/token']
            );
        }

        return $next($request, $response);
    }
}
