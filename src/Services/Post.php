<?php

namespace Mongolium\Services;

use Mongolium\Services\Db\Orm;
use Mongolium\Model\Post as PostModel;

class Post
{
    protected $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function read()
    {
        return $this->orm->all(PostModel::class);
    }
}
