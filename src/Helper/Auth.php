<?php

namespace Helium\Helper;

use Helium\Exceptions\Auth as AuthException;
use Slim\Http\Request;

trait Auth
{
    public function getAuthorizationAttribute(Request $request, string $attribute): string
    {
        if ($request->hasHeader('Authorization')) {
            $authHeader = $request->getHeaderLine('Authorization');

            $authParts = explode(',', $authHeader);

            $authPart = array_filter($authParts, function($row) use ($attribute) {
                return preg_match('|^' . trim($attribute) . '\s|', $row);
            });

            if (count($authPart) === 1) {
                $tokenParts = explode(' ', $authPart[0]);

                return $tokenParts[1];
            }
        }

        return '';
    }

    public function getBearerToken(Request $request): string
    {
        return $this->getAuthorizationAttribute($request, 'Bearer');
    }

    public function getBasicAuth(Request $request): string
    {
        return $this->getAuthorizationAttribute($request, 'Basic');
    }
}
