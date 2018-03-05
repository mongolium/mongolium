<?php

namespace Helium\Exceptions;

use Exception;

class TokenException extends Exception
{
    public function __construct(string $message, int $code = 0, $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
