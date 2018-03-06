<?php

namespace Mongolium\Services\Db;

use Mongolium\Services\Db\Hydrator;

abstract class BaseModel implements Hydrator
{
    protected $id;

    protected static $table;

    protected static $unique;

    public static function getTable(): string
    {
        return static::$table;
    }

    public static function getUnique(): array
    {
        return static::$unique ?? [];
    }

    public function setId(string $id)
    {
        $this->id = $id;
    }
}
