<?php

namespace Mongolium\Core\Services\Db;

use Mongolium\Core\Services\Db\Hydrator;

abstract class BaseModel implements Hydrator
{
    protected $id;

    protected static $table;

    protected static $unique;

    protected $hide;

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

    public function hide(): array
    {
        return array_filter($this->extract(), function ($key) {
            return !in_array($key, $this->hide);
        }, ARRAY_FILTER_USE_KEY);
    }
}
