<?php

namespace Tests\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Mongolium\Helper\Slug;

class SlugTest extends TestCase
{
    use Slug;

    public function testMakeSlug()
    {
        $this->assertEquals('this-is-a-title', $this->makeSlug('This Is a Title'));
    }

    public function testMakeSlugSpecialChracters()
    {
        $this->assertEquals(
            'this-is-a-title-something-else-and-something-else',
            $this->makeSlug('This $ Is # a Title & Something ! * Else £ And $ Some"thi\'ng ^ Else')
        );
    }

}
