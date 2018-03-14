<?php

namespace Mongolium\Model;

use Mongolium\Services\Db\BaseModel;
use Mongolium\Services\Db\Hydrator;

class Page extends BaseModel
{
    protected $title;

    protected $slug;

    protected $description;

    protected $text;

    protected $tags;

    protected $creatorId;

    protected $createdAt;

    protected $updatedAt;

    protected static $table = 'pages';

    protected static $unique = ['title', 'slug'];

    public function __construct(
        string $id,
        string $title,
        string $slug,
        string $description,
        string $text,
        array $tags,
        string $creatorId,
        string $createdAt,
        string $updatedAt
    )
    {
        $this->id = $id;

        $this->title = $title;

        $this->slug = $slug;

        $this->description = $description;

        $this->text = $text;

        $this->tags = $tags;

        $this->creatorId = $creatorId;

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
            $data['creator_id'],
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
            'creator_id' => $this->creatorId,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt
        ];
    }
}
