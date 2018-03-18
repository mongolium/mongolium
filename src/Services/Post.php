<?php

namespace Mongolium\Services;

use Mongolium\Services\Db\Orm;
use Mongolium\Model\Post as PostModel;
use ReallySimple\Collection;
use Carbon\Carbon;

class Post
{
    protected $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function getPublished(): Collection
    {
        return $this->orm->all(
            PostModel::class,
            [
                'publish' => true,
                'publish_at' =>
                [
                    '$lt' => Carbon::now()->toDateTimeString()
                ]
            ]
        );
    }

    public function getPost(string $id): PostModel
    {
        return $this->orm->find(PostModel::class, ['id' => $id]);
    }
}
