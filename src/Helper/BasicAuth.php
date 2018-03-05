<?php

namespace Helium\Helper;

trait BasicAuth
{
    public function decode(string $basicAuthString): array
    {
        $decode = base64_decode($basicAuthString);

        $parts = explode(':', $decode);

        if (count($parts) === 2) {
            return [
                'username' => $parts[0],
                'password' => $parts[1],
            ];
        }

        return [];
    }

    public function encode(string $username, string $password): string
    {
        return base64_encode($username . ':' . $password);
    }
}
