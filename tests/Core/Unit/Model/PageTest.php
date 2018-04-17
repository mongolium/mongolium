<?php

namespace Tests\Core\Unit\Model;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Model\Page;

class PageTest extends TestCase
{
    public function testPageHydate()
    {
        $page = Page::hydrate(
            [
                'id' => '1a',
                'title' => 'the page',
                'slug' => 'the-page',
                'description' => 'this is a page',
                'text' => 'this page has some information on it',
                'tags' => ['foo', 'bar'],
                'creator_id' => '3c',
                'publish' => true,
                'created_at' => '2018-03-13 13:40:02',
                'updated_at' => '2018-03-13 13:40:02'
            ]
        );

        $this->assertInstanceOf(Page::class, $page);
    }

    public function testPageExtract()
    {
        $page = Page::hydrate(
            [
                'id' => '1a',
                'title' => 'the page',
                'slug' => 'the-page',
                'description' => 'this is a page',
                'text' => 'this page has some information on it',
                'tags' => ['foo', 'bar'],
                'creator_id' => '3c',
                'publish' => true,
                'created_at' => '2018-03-13 13:40:02',
                'updated_at' => '2018-03-13 13:40:02'
            ]
        );

        $data = $page->extract();

        $this->assertSame(10, count($data));
        $this->assertTrue(isset($data['id']));
        $this->assertTrue(isset($data['title']));
        $this->assertTrue(isset($data['slug']));
        $this->assertTrue(isset($data['description']));
        $this->assertTrue(isset($data['text']));
        $this->assertTrue(isset($data['tags']));
        $this->assertTrue(isset($data['creator_id']));
        $this->assertTrue(isset($data['publish']));
        $this->assertTrue(isset($data['created_at']));
        $this->assertTrue(isset($data['updated_at']));
    }
}
