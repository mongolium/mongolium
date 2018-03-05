<?php

namespace Mongolium\Middleware;

use Slim\Http\Request;
use Slim\Http\Response;
use Mongolium\Helper\Auth;

class Basic
{
    use Auth;

    public function __invoke(Request $request, Response $response, $next)
    {
        $request = $request->withAttribute('bearer_token', $this->getBearerToken($request));

        $request = $request->withAttribute('basic_auth', $this->getBasicAuth($request));

        return $next($request, $response);
    }
}
