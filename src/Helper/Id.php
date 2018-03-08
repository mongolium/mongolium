<?php

namespace Mongolium\Helper;

trait Id
{
    public function uniqueId()
    {
        return uniqid('mnglm_');
    }
}
