<?php

namespace Mongolium\Core\Model;

use Mongolium\Core\Services\Db\BaseModel;
use Mongolium\Core\Services\Db\Hydrator;

class Post extends BaseModel
{
    protected $title;

    protected $slug;

    protected $description;

    protected $text;

    protected $tags;

    protected $authorId;

    protected $creatorId;

    protected $publish;

    protected $publishAt;

    protected $createdAt;

    protected $updatedAt;

    protected static $table = 'posts';

    protected static $unique = ['title', 'slug'];

    public function __construct(
        string $id,
        string $title,
        string $slug,
        string $description,
        string $text,
        array $tags,
        string $authorId,
        string $creatorId,
        bool $publish,
        string $publishAt,
        string $createdAt,
        string $updatedAt
    ) {
        $this->id = $id;

        $this->title = $title;

        $this->slug = $slug;

        $this->description = $description;

        $this->text = $text;

        $this->tags = $tags;

        $this->authorId = $authorId;

        $this->creatorId = $creatorId;

        $this->publish = $publish;

        $this->publishAt = $publishAt;

        $this->createdAt = $createdAt;

        $this->updatedAt = $updatedAt;
    }

    public static function hydrate(array $data): Hydrator
    {
        return new static(
            $data['id'],
            $data['title'],
            $data['slug'],
            $data['description'],
            $data['text'],
            $data['tags'],
            $data['author_id'],
            $data['creator_id'],
            $data['publish'],
            $data['publish_at'],
            $data['created_at'],
            $data['updated_at']
        );
    }

    public function extract(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'text' => $this->text,
            'tags' => $this->tags,
            'author_id' => $this->authorId,
            'creator_id' => $this->creatorId,
            'publish' => $this->publish,
            'publish_at' => $this->publishAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
