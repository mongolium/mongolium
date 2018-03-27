<?php

namespace Mongolium\Core\Helper;

trait Id
{
    public function uniqueId()
    {
        return uniqid('mnglm_');
    }
}
