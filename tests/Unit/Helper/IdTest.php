<?php

namespace Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Mongolium\Helper\Id;

class IdTest extends TestCase
{
    use Id;

    public function testMakeId()
    {
        $this->assertTrue(is_string($this->uniqueId()));
    }
}
