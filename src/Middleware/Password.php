<?php

namespace Helium\Middleware;

use Helium\Helper\JsonResponse as JsonResponseHelper;
use Helium\Services\JsonResponse;
use Helium\Helper\Id;
use Helium\Helper\BasicAuth;

class Password
{
    use JsonResponseHelper, Id, BasicAuth;

    public function __invoke($request, $response, $next)
    {
        if (empty($request->getAttribute('basic_auth'))) {
            return $this->jsonResponse(
                $response,
                new JsonResponse(
                    400,
                    'Bad Request: Please supply a username and password with your request',
                    $this->generateUniqueId(),
                    'error',
                    [],
                    ['token' => '/token']
                )
            );
        }

        $userPassword = $this->decode($request->getAttribute('basic_auth'));

        if (count($userPassword) === 0 || empty($userPassword['username']) || empty($userPassword['password'])) {
            return $this->jsonResponse(
                $response,
                new JsonResponse(
                    400,
                    'Bad Request: Please supply a valid username and password with your request',
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
