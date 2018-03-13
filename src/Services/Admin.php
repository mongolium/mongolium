<?php

namespace Mongolium\Services;

use Mongolium\Services\Db\Orm;
use Mongolium\Model\Admin as AdminModel;
use ReallySimple\Collection;

class Admin
{
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function create(array $data): AdminModel
    {
        return $this->orm->create(AdminModel::class, $data);
    }

    public function read(): Collection
    {
        return $this->orm->all(AdminModel::class);
    }
}
