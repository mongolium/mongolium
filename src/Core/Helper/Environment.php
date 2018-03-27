<?php

namespace Mongolium\Core\Helper;

use Mongolium\Core\Exceptions\EnvironmentException;

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
