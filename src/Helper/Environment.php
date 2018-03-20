<?php

namespace Mongolium\Helper;

use Mongolium\Exceptions\EnvironmentException;

trait Environment
{
    public function env(string $attribute)
    {
        $env = getenv($attribute);

        if (!empty($env)) {
            return $env;
        }

        throw new EnvironmentException('The environment variable ' . $attribute . ' could not be found');
    }
}
