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

    /**
     * @expectedException Mongolium\Exceptions\EnvironmentException
     * @expectedExceptionMessage The environment variable CAR could not be found
     */
    public function testNoEnvVariable()
    {
        $this->env('CAR');
    }
}
