<?php

namespace Mongolium\Services;

use Mongolium\Services\Db\Orm;
use Mongolium\Model\Post as PostModel;
use ReallySimple\Collection;
use Carbon\Carbon;
use Mongolium\Helper\Slug;

class Post
{
    use Slug;

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

    public function create(array $data): PostModel
    {
        $data['created_at'] = Carbon::now()->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();
        $data['slug'] = $this->makeSlug($data['title']);

        return $this->orm->create(PostModel::class, $data);
    }

    public function update(array $data): PostModel
    {
        $data['updated_at'] = Carbon::now()->toDateTimeString();
        $data['slug'] = $this->makeSlug($data['title']);

        return $this->orm->update(PostModel::class, ['id' => $data['id']], $data);
    }

    public function delete(string $id): bool
    {
        return $this->orm->delete(PostModel::class, ['id' => $id]);
    }
}
