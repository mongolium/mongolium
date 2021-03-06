<?php

namespace Mongolium\Core\Services;

use Mongolium\Core\Services\Db\Orm;
use Mongolium\Core\Model\Admin as AdminModel;
use ReallySimple\Collection;
use Carbon\Carbon;

class Admin
{
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function create(array $data): AdminModel
    {
        $data['created_at'] = Carbon::now()->toDateTimeString();
        $data['updated_at'] = Carbon::now()->toDateTimeString();

        return $this->orm->create(AdminModel::class, $data);
    }

    public function update(array $data): AdminModel
    {
        $data['updated_at'] = Carbon::now()->toDateTimeString();

        return $this->orm->update(AdminModel::class, ['id' => $data['id']], $data);
    }

    public function all(): Collection
    {
        return $this->orm->all(AdminModel::class);
    }

    public function getAdmin(string $id): AdminModel
    {
        return $this->orm->find(AdminModel::class, ['id' => $id]);
    }

    public function delete(string $id): bool
    {
        return $this->orm->delete(AdminModel::class, ['id' => $id]);
    }
}
