<?php

namespace Mongolium\Services;

use Mongolium\Services\Db\Orm;
use Mongolium\Model\User as UserModel;
use ReallySimple\Collection;

class User
{
    private $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function create(array $data): UserModel
    {
        return $this->orm->create(UserModel::class, $data);
    }

    public function read(): Collection
    {
        return $this->orm->all(UserModel::class);
    }
}
