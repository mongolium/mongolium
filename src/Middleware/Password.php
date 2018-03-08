<?php

namespace Mongolium\Middleware;

use Mongolium\Services\Response\Response;
use Mongolium\Helper\BasicAuth;
use Slim\Http\Response as SlimResponse;
use Slim\Http\Request;

class Password
{
    use BasicAuth;

    public function __invoke(Request $request, SlimResponse $response, $next): SlimResponse
    {
        if (empty($request->getAttribute('basic_auth'))) {
            return Response::make()->respond400(
                $response,
                'Please supply a username and password with your request',
                ['token' => '/token']
            );
        }

        $userPassword = $this->decode($request->getAttribute('basic_auth'));

        if (count($userPassword) === 0 || empty($userPassword['username']) || empty($userPassword['password'])) {
            return Response::make()->respond400(
                $response,
                'Please supply a valid username and password with your request',
                ['token' => '/token']
            );
        }

        return $next($request, $response);
    }
}
