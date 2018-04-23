<?php

namespace Mongolium\Core\Services\Db;

use Mongolium\Core\Services\Db\Client;
use Mongolium\Core\Services\Db\Hydratpr;
use Mongolium\Core\Exceptions\OrmException;
use Mongolium\Core\Exceptions\ClientException;
use Mongolium\Core\Services\Db\BaseModel;
use Mongolium\Core\Services\Db\BaseOrm;
use ReallySimple\Collection;
use MongoDB\Driver\Exception\AuthenticationException;
use MongoDB\Driver\Exception\ConnectionTimeoutException;
use Error;

/**
 * ORM interface providing logical database methods to return data and hydrate
 * entity models.
 *
 * @author Rob Waller <rdwaller1984@googlemail.com>
 */
class Orm extends BaseOrm
{
    /**
     * Inject mongo client
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Find a mongo document, hydrate and return entity model
     *
     * @param string $entity
     * @param array $query
     */
    public function find(string $entity, array $query): BaseModel
    {
        $result = $this->findOneAsArray($entity, $query);

        if (count($result) > 0) {
            return $entity::hydrate($result);
        }

        throw new OrmException('No ' . $entity . ' record found to match this query ' . print_r($query, true) . '.');
    }

    /**
     * Find multiple mongo documents and return a collection of entity models
     *
     * @param string $entityString
     * @param array $query
     * @return Collection
     */
    public function all(string $entityString, array $query = []): Collection
    {
        $result = $this->findAsArray($entityString, $query);

        $items = [];

        if (count($result) > 0) {
            foreach ($result as $item) {
                $entity = $entityString::hydrate($this->mongoToEntity($item));

                $items[] = $entity;
            }

            return new Collection($items);
        }

        throw new OrmException('No ' . $entityString . ' record found to match this query ' . print_r($query, true) . '.');
    }

    /**
     * Count the number of mongo documents that exist within a collection based
     * a defined query.
     *
     * @param string $entity
     * @param array $query
     * @return int
     */
    public function count(string $entity, array $query): int
    {
        try {
            return $this->client->getCollection($entity::getTable())->count($query);
        } catch (AuthenticationException $e) {
            throw new ClientException('Could not connect to database: ' . $e->getMessage());
        } catch (ConnectionTimeoutException $e) {
            throw new ClientException('Could not connect to database: ' . $e->getMessage());
        }
    }

    /**
     * Create a mongo document based on an entity model
     *
     * @param string $entityString
     * @param array $data
     * @return BaseModel
     */
    public function create(string $entityString, array $data): BaseModel
    {
        try {
            $entity = $entityString::hydrate($this->emptyEntityId($data));

            if (!$this->hasId($entity) && !$this->exists($entity)) {
                $result = $this->insertOne($entity);

                $entity->setId($result->getInsertedId());

                return $entity::hydrate($entity->extract());
            }
        } catch (Error $e) {
            throw new OrmException('Could not create ' . $entityString . ' model: ' . $e->getMessage());
        }

        throw new OrmException('Cannot duplicate this ' . get_class($entity) . ' record, already exists.');
    }

    /**
     * Update an existing mongo document based on a defined query and return an
     * entity model
     *
     * @param string $entity
     * @param array $query
     * @param array $data
     * @return BaseModel
     */
    public function update(string $entity, array $query, array $data): BaseModel
    {
        $this->entityHasData($entity, $data);

        try {
            $collection = $this->client->getCollection($entity::getTable());

            $data = ['$set' => $this->makeObjectId($data)];

            $result = $collection->updateOne($this->makeObjectId($query), $data);

            if ($result) {
                return $this->find($entity, $this->makeObjectId($query));
            }
        } catch (Error $e) {
            throw new OrmException('Could not update ' . $entity . ' model: ' . $e->getMessage());
        }

        throw new OrmException('Could not update ' . $entity . ' model');
    }

    /**
     * Update multipe existing mongo document based on a defined query and
     * return a collection of entity models
     *
     * @param string $entity
     * @param array $query
     * @param array $data
     * @return Collection
     */
    public function updateMany(string $entity, array $query, array $data): Collection
    {
        $this->entityHasData($entity, $data);

        try {
            $collection = $this->client->getCollection($entity::getTable());

            $set = ['$set' => $this->makeObjectId($data)];

            $result = $collection->updateMany($this->makeObjectId($query), $set);

            if ($result) {
                return $this->all($entity, $this->makeObjectId($data));
            }
        } catch (Error $e) {
            throw new OrmException('Could not update ' . $entity . ' model: ' . $e->getMessage());
        }

        throw new OrmException('Could not update ' . $entity . ' model');
    }

    /**
     * Check to see if a Mongo document exists based on the unique property set
     * on the entity model.
     *
     * @param BaseModel $entity
     * @return bool
     */
    public function exists(BaseModel $entity): bool
    {
        $unique = $entity->getUnique();

        $filter = array_filter($entity->extract(), function ($key) use ($unique) {
            return in_array($key, $unique);
        }, ARRAY_FILTER_USE_KEY);

        return $this->count(get_class($entity), ['$or' => [$filter]]) >= 1;
    }

    /**
     * Delete a mongo document based on a query
     *
     * @param string $entity
     * @param array $query
     * @return bool
     */
    public function delete(string $entity, array $query): bool
    {
        try {
            $collection = $this->client->getCollection($entity::getTable());

            $collection->deleteOne($this->makeObjectId($query));

            return true;
        } catch (Error $e) {
            throw new OrmException('Could not delete ' . $entity . ' model: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Drop a mongo collection from the database. A dangerous method...
     *
     * @param string $entity
     * @return bool
     */
    public function drop(string $entity): bool
    {
        try {
            $result = $this->client->getCollection($entity::getTable())->drop();

            return is_array($result) && isset($result['ok']) && $result['ok'] === 1;
        } catch (AuthenticationException $e) {
            throw new ClientException('Could not connect to database: ' . $e->getMessage());
        } catch (ConnectionTimeoutException $e) {
            throw new ClientException('Could not connect to database: ' . $e->getMessage());
        }
    }
}
