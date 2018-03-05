<?php

namespace Mongolium\Helper;

trait Id
{
    public function generateUniqueId()
    {
        return uniqid('mnglm_');
    }
}
