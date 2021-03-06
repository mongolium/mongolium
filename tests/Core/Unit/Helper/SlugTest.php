<?php

namespace Tests\Core\Unit\Helper;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Helper\Slug;

class SlugTest extends TestCase
{
    use Slug;

    public function testMakeSlug()
    {
        $this->assertSame('this-is-a-title', $this->makeSlug('This Is a Title'));
    }

    public function testMakeSlugSpecialChracters()
    {
        $this->assertSame(
            'this-is-a-title-something-else-and-something-else',
            $this->makeSlug('This $ Is # a Title & Something ! * Else £ And $ Some"thi\'ng ^ Else')
        );
    }

    public function testDashTitle()
    {
        $this->assertSame(
            'this-is-a-title',
            $this->makeSlug('this--is----a---title')
        );
    }

    public function testTitleWithNumbers()
    {
        $this->assertSame(
            'this-is-title-number-04',
            $this->makeSlug('this--is-----  title number 04')
        );
    }

    public function testOkTitle()
    {
        $this->assertSame(
            'this-is-a-title',
            $this->makeSlug('this-is-a-title')
        );
    }
}
