<?php

namespace Mongolium\Core\Services\Db;

use Mongolium\Core\Services\Db\Client;
use Mongolium\Core\Services\Db\Hydratpr;
use Mongolium\Core\Exceptions\OrmException;
use Mongolium\Core\Services\Db\BaseModel;
use MongoDB\InsertOneResult;
use MongoDB\BSON\ObjectId;
use ReallySimple\Collection;
use ReflectionClass;
use Error;
use MongoDB\Model\BSONArray;

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

    public function exists(BaseModel $entity): bool
    {
        $unique = $entity->getUnique();

        $filter = array_filter($entity->extract(), function ($key) use ($unique) {
            return in_array($key, $unique);
        }, ARRAY_FILTER_USE_KEY);

        return $this->count(get_class($entity), ['$or' => [$filter]]) >= 1;
    }

    public function buildId(array $data): array
    {
        if (isset($data['id'])) {
            $data['_id'] = new ObjectId($data['id']);
            unset($data['id']);
        }

        return $data;
    }

    public function cleanId(array $data): array
    {
        $data['id'] = '';
        return $data;
    }

    public function cleanDbId($data): array
    {
        $data = get_object_vars($data);
        $data['id'] = (string) $data['_id'];
        unset($data['_id']);
        return $data;
    }

    public function cleanFields(array $data): array
    {
        return array_map(function ($item) {
            if ($item instanceof BSONArray) {
                return $item->getArrayCopy();
            }

            return $item;
        }, $data);
    }

    public function clean($data): array
    {
        $data = $this->cleanDbId($data);

        return $this->cleanFields($data);
    }

    public function hasId(BaseModel $entity): bool
    {
        $data = $entity->extract();

        return !empty($data['id']);
    }

    public function getEntityProperties(string $entity): array
    {
        $reflector = new ReflectionClass($entity);

        $parameters = $reflector->getMethod('__construct')->getParameters();

        if (count($parameters) >= 1) {
            $keys = [];

            foreach ($parameters as $param) {
                $keys[] = $param->getName();
            }

            return $keys;
        }

        throw new OrmException('Invalid Model ' . $entity . ' constructor has no parameters.');
    }

    public function validateData(string $entity, array $data)
    {
        $entityKeys = $this->getEntityProperties($entity);
        $dataKeys = array_keys($data);

        $filter = array_filter($entityKeys, function ($key) use ($dataKeys) {
            return in_array($key, $dataKeys);
        });

        if (count($filter) === 0) {
            throw new OrmException('Data ' . print_r($data, true) . ' not valid for model ' . $entity);
        }
    }

    public function findAsArray(string $entity, array $query = []): array
    {
        return $this->collection($entity::getTable())->find($this->buildId($query))->toArray();
    }

    public function findOneAsArray(string $entity, array $query = []): array
    {
        $result = $this->findAsArray($entity, $query);

        if (count($result) > 0) {
            return $this->clean($result[0]);
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

    public function all(string $entityString, array $query = []): Collection
    {
        $result = $this->findAsArray($entityString, $query);

        $items = [];

        if (count($result) > 0) {
            foreach ($result as $item) {
                $entity = $entityString::hydrate($this->clean($item));

                $items[] = $entity;
            }

            return new Collection($items);
        }

        throw new OrmException('No ' . $entityString . ' record found to match this query ' . print_r($query, true) . '.');
    }

    public function count(string $entity, array $query): int
    {
        return $this->collection($entity::getTable())->count($query);
    }

    public function insertEntity(BaseModel $entity): InsertOneResult
    {
        $collection = $this->collection($entity::getTable());

        $data = $entity->extract();

        unset($data['id']);

        return $collection->insertOne($data);
    }

    public function create(string $entityString, array $data): BaseModel
    {
        try {
            $entity = $entityString::hydrate($this->cleanId($data));

            if (!$this->hasId($entity) && !$this->exists($entity)) {
                $result = $this->insertEntity($entity);

                $entity->setId($result->getInsertedId());

                return $entity::hydrate($entity->extract());
            }
        } catch (Error $e) {
            throw new OrmException('Could not create ' . $entityString . ' model: ' . $e->getMessage());
        }

        throw new OrmException('Cannot duplicate this ' . get_class($entity) . ' record, already exists.');
    }

    public function update(string $entity, array $query, array $data): BaseModel
    {
        $this->validateData($entity, $data);

        try {
            $collection = $this->collection($entity::getTable());

            $data = ['$set' => $this->buildId($data)];

            $result = $collection->updateOne($this->buildId($query), $data);

            if ($result) {
                return $this->find($entity, $this->buildId($query));
            }
        } catch (Error $e) {
            throw new OrmException('Could not update ' . $entity . ' model: ' . $e->getMessage());
        }

        throw new OrmException('Could not update ' . $entity . ' model');
    }

    public function updateMany(string $entity, array $query, array $data)
    {
        $this->validateData($entity, $data);

        try {
            $collection = $this->collection($entity::getTable());

            $set = ['$set' => $this->buildId($data)];

            $result = $collection->updateMany($this->buildId($query), $set);

            if ($result) {
                return $this->all($entity, $this->buildId($data));
            }
        } catch (Error $e) {
            throw new OrmException('Could not update ' . $entity . ' model: ' . $e->getMessage());
        }

        throw new OrmException('Could not update ' . $entity . ' model');
    }

    public function delete(string $entity, array $query): bool
    {
        try {
            $collection = $this->collection($entity::getTable());

            $collection->deleteOne($this->buildId($query));

            return true;
        } catch (Error $e) {
            throw new OrmException('Could not delete ' . $entity . ' model: ' . $e->getMessage());
        }

        return false;
    }

    public function drop(string $entity): bool
    {
        $result = $this->collection($entity::getTable())->drop();

        return is_array($result) && isset($result['ok']) && $result['ok'] === 1;
    }
}
