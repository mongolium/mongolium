<?php

namespace Helium\Helper;

trait Environment
{
    public function env(string $environmentAttribute)
    {
        return getenv($environmentAttribute);
    }
}
