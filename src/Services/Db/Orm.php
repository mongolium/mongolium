<?php

namespace Mongolium\Services\Db;

use Mongolium\Services\Db\Client;
use Mongolium\Services\Db\Hydratpr;
use Mongolium\Exceptions\OrmException;
use Mongolium\Services\Db\BaseModel;

class Orm
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function collection(string $table)
    {
        return $this->client->getConnection()->{$this->client->getDatabase()}->{$table};
    }

    public function getUnique(): array
    {
        return $this->unique ?? [];
    }

    public function exists(BaseModel $entity): bool
    {
        $unique = $entity->getUnique();

        $filter = array_filter($entity->extract(), function ($key) use ($unique) {
            return in_array($key, $unique);
        }, ARRAY_FILTER_USE_KEY);

        return $this->count(get_class($entity), ['$or' => [$filter]]) >= 1;
    }

    public function cleanId(array $data): array
    {
        $data['id'] = '';
        return $data;
    }

    public function cleanDbId(array $data): array
    {
        $data['id'] = (string) $data['_id'];
        unset($data['_id']);
        return $data;
    }

    public function hasId(BaseModel $entity): bool
    {
        $data = $entity->extract();

        return !empty($data['id']);
    }

    public function findAsArray(string $entity, array $query): array
    {
        return $this->collection($entity::getTable())->find($query)->toArray();
    }

    public function findOneAsArray(string $entity, array $query): array
    {
        $result = $this->findAsArray($entity, $query);

        if (count($result) > 0) {
            return $this->cleanDbId(get_object_vars($result[0]));
        }

        return [];
    }

    public function find(string $entity, array $query): BaseModel
    {
        $result = $this->findOneAsArray($entity, $query);

        if (count($result) > 0) {
            return $entity::hydrate($result);
        }

        throw new OrmException('No ' . $entity . ' record found to match this query ' . print_r($query, true) . '.');
    }

    public function all(string $entity, array $query): Collection
    {
        $result = $this->collection($entity::getTable())->find($query)->toArray();

        if (count($result) > 0) {
            //return $entity::hydrate($result[0]);
        }

        throw new OrmException('No ' . $entity . ' record found to match this query ' . print_r($query, true) . '.');
    }

    public function count(string $entity, array $query): int
    {
        return $this->collection($entity::getTable())->count($query);
    }

    public function create(string $entityString, array $data): BaseModel
    {
        $entity = $entityString::hydrate($this->cleanId($data));

        if (!$this->hasId($entity) && !$this->exists($entity)) {

            $collection = $this->collection($entity::getTable());

            $data = $entity->extract();

            unset($data['id']);

            $result = $collection->insertOne($data);

            $entity->setId($result->getInsertedId());

            return $entity::hydrate($entity->extract());
        }

        throw new OrmException('Cannot duplicate this ' . get_class($entity) . ' record, already exists.');
    }

    public function update(Hydrate $entity)
    {

    }

    public function delete(Hydrate $entity)
    {

    }

    public function drop(string $entity): bool
    {
        $result = $this->collection($entity::getTable())->drop();

        return is_array($result) && isset($result['ok']) && $result['ok'] === 1;
    }
}
