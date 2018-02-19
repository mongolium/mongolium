<?php

namespace Helium\Helper;

use Helium\Exceptions\Auth as AuthException;

trait Auth
{
    public function getBearerFromAuthorisationHeader(string $authorisationHeaderString): string
    {
        $parts = explode(",", $authorisationHeaderString);

        $filter = array_filter($parts, function($row){
            return preg_match('|^Bearer\s|', trim($row));
        });

        $filter = array_values($filter);

        if (count($filter) === 1) {
            return trim($filter[0]);
        }

        throw new AuthException('No Bearer found in Authorization Header');
    }

    public function getJWTFromBearer(string $bearerString): string
    {
        $parts = explode(" ", $bearerString);

        if (isset($parts[1])) {
            return $parts[1];
        }

        throw new AuthException('Authorization Header Bearer empty, please include a JSON Web Token');
    }
}
