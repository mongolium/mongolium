<?php

namespace Tests\Core\Unit\Model;

use PHPUnit\Framework\TestCase;
use Mongolium\Core\Model\Post;

class PostTest extends TestCase
{
    public function testPostHydate()
    {
        $post = Post::hydrate(
            [
                'id' => '1a',
                'title' => 'the post',
                'slug' => 'the-post',
                'description' => 'this is a post',
                'text' => 'this post is a story about stuff',
                'tags' => ['foo', 'bar'],
                'author_id' => '2b',
                'creator_id' => '3c',
                'publish' => true,
                'publish_at' => '2018-03-13 13:40:02',
                'created_at' => '2018-03-13 13:40:02',
                'updated_at' => '2018-03-13 13:40:02'
            ]
        );

        $this->assertInstanceOf(Post::class, $post);
    }

    public function testPostExtract()
    {
        $post = Post::hydrate(
            [
                'id' => '1a',
                'title' => 'the post',
                'slug' => 'the-post',
                'description' => 'this is a post',
                'text' => 'this post is a story about stuff',
                'tags' => ['foo', 'bar'],
                'author_id' => '2b',
                'creator_id' => '3c',
                'publish' => true,
                'publish_at' => '2018-03-13 13:40:02',
                'created_at' => '2018-03-13 13:40:02',
                'updated_at' => '2018-03-13 13:40:02'
            ]
        );

        $data = $post->extract();

        $this->assertSame(12, count($data));
        $this->assertTrue(isset($data['id']));
        $this->assertTrue(isset($data['title']));
        $this->assertTrue(isset($data['slug']));
        $this->assertTrue(isset($data['description']));
        $this->assertTrue(isset($data['text']));
        $this->assertTrue(isset($data['tags']));
        $this->assertTrue(isset($data['author_id']));
        $this->assertTrue(isset($data['creator_id']));
        $this->assertTrue(isset($data['publish']));
        $this->assertTrue(isset($data['publish_at']));
        $this->assertTrue(isset($data['created_at']));
        $this->assertTrue(isset($data['updated_at']));
    }
}
