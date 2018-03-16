<?php

namespace Mongolium\Services;

use Mongolium\Services\Db\Orm;
use Mongolium\Model\Page as PageModel;
use ReallySimple\Collection;

class Page
{
    protected $orm;

    public function __construct(Orm $orm)
    {
        $this->orm = $orm;
    }

    public function read(): Collection
    {
        return $this->orm->all(PageModel::class);
    }
}
