<?php

namespace Mongolium\Core\Services;

use Mongolium\Core\Services\Db\Orm;
use Mongolium\Core\Model\Page as PageModel;
use ReallySimple\Collection;
use Carbon\Carbon;
use Mongolium\Core\Helper\Slug;

class Page
{
    use Slug;

    protected $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function getPublished(): Collection
    {
        return $this->orm->all(PageModel::class, ['publish' => true]);
    }

    public function getPage(string $id): PageModel
    {
        return $this->orm->find(PageModel::class, ['id' => $id]);
    }

    public function create(array $data): PageModel
    {
        $data['created_at'] = Carbon::now()->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();
        $data['slug'] = $this->makeSlug($data['title']);

        return $this->orm->create(PageModel::class, $data);
    }

    public function update(array $data): PageModel
    {
        $data['updated_at'] = Carbon::now()->toDateTimeString();
        $data['slug'] = $this->makeSlug($data['title']);

        return $this->orm->update(PageModel::class, ['id' => $data['id']], $data);
    }

    public function delete(string $id): bool
    {
        return $this->orm->delete(PageModel::class, ['id' => $id]);
    }
}
