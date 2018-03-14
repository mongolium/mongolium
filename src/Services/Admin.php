<?php

namespace Mongolium\Services;

use Mongolium\Services\Db\Orm;
use Mongolium\Model\Admin as AdminModel;
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

    public function read(): Collection
    {
        return $this->orm->all(AdminModel::class);
    }
}
