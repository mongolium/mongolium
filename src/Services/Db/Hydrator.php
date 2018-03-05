<?php

namespace Helium\Services\Db;

interface Hydrator
{
    public static function hydrate(array $data): Hydrator;

    public function extract(): array;
}
