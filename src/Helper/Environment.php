<?php

namespace Mongolium\Helper;

trait Environment
{
    public function env(string $environmentAttribute)
    {
        return getenv($environmentAttribute);
    }
}
