<?php

namespace Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Mongolium\Helper\Environment;

class EnvironmentTest extends TestCase
{
    use Environment;

    public function testGetVariable()
    {
        putenv('FOO=bar');

        $this->assertEquals('bar', $this->env('FOO'));
    }
}
