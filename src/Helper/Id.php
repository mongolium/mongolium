<?php

namespace Helium\Helper;

trait Id
{
    public function generateUniqueId()
    {
        return uniqid('mnglm_');
    }
}
