<?php

namespace Helium\Services;

use Helium\Services\Db\Orm;
use Helium\Model\User as UserModel;

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
}
