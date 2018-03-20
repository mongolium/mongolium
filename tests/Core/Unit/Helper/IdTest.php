<?php

namespace Tests\Core\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Helper\Id;

class IdTest extends TestCase
{
    use Id;

    public function testMakeId()
    {
        $this->assertTrue(is_string($this->uniqueId()));
    }
}
