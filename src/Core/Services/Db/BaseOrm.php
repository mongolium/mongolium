<?php

namespace Mongolium\Core\Services\Db;

use Mongolium\Core\Services\Db\Client;
use Mongolium\Core\Exceptions\OrmException;
use MongoDB\InsertOneResult;
use MongoDB\Model\BSONArray;
use MongoDB\BSON\ObjectId;
use MongoDB\Model\BSONDocument;
use ReflectionClass;
use Error;

/**
 * Base ORM provides functionality to process models and interact with MongoDB
 *
 * @author Rob Waller <rdwaller1984@googlemail.com>
 */
class BaseOrm
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Converts an entity model id to a Mongo object id.
     *
     * @param array $data
     * @return array
     */
    public function makeObjectId(array $data): array
    {
        if (isset($data['id'])) {
            $data['_id'] = new ObjectId($data['id']);
            unset($data['id']);
        }

        return $data;
    }

    /**
     * Emptys an entity id if it is not required. For example on an insert.
     *
     * @param array $data
     * @return array
     */
    public function emptyEntityId(array $data): array
    {
        $data['id'] = '';
        return $data;
    }

    /**
     * Conver an Mongo object id to an entity model id, does the oposite of
     * makeObjectId method.
     *
     * @param BSONDocument $data
     * @return array
     */
    public function makeEntityId(BSONDocument $data): array
    {
        $data = get_object_vars($data);
        $data['id'] = (string) $data['_id'];
        unset($data['_id']);
        return $data;
    }

    /**
     * Convert a Mongo BSON arrays to a standard PHP arrays for use in an
     * enity model
     *
     * @param array $data
     * @return array
     */
    public function makeEntityArray(array $data): array
    {
        return array_map(function ($item) {
            if ($item instanceof BSONArray) {
                return $item->getArrayCopy();
            }

            return $item;
        }, $data);
    }

    /**
     * Convert a return mongo document array to an entity model array ready for
     * entity hydration
     *
     * @param BSONDocument $data
     * @return array
     */
    public function mongoToEntity(BSONDocument $data): array
    {
        $data = $this->makeEntityId($data);

        return $this->makeEntityArray($data);
    }

    /**
     * Check that an entity model has an id
     *
     * @param BaseModel $entity
     * @return bool
     */
    public function hasId(BaseModel $entity): bool
    {
        return !empty($entity->extract()['id']);
    }

    /**
     * Use reflection to retrieve the property names as an array from the
     * entity model constructor
     *
     * @param string $entity
     * @return array
     */
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

    /**
     * Transform key parts from snake case to came case.
     *
     * @param array $keys
     * @return string
     */
    public function keyPartsToKey(array $keys): string
    {
        return array_reduce($keys, function ($carry, $key) {
            $carry .= ucfirst($key);
            return lcfirst($carry);
        }, '');
    }

    /**
     * Get the keys held in the data array but so they match the camel case
     * formatting of the entity constructor rather than the snake case of the
     * entity extractor.
     *
     * @param array $data
     * @return array
     */
    public function getDataKeys(array $data): array
    {
        return array_reduce(array_keys($data), function ($carry, $key) use ($data) {
            if (strpos($key, '_') !== false) {
                $carry[] = $this->keyPartsToKey(explode('_', $key));
                return $carry;
            }

            $carry[] = $key;
            return $carry;
        }, []);
    }

    /**
     * Check the entity has the data fields that are to be updated.
     *
     * @param string $entity
     * @param array $data
     * @return bool
     */
    public function entityHasData(string $entity, array $data): bool
    {
        $entityKeys = $this->getEntityProperties($entity);
        $dataKeys = $this->getDataKeys($data);

        $filter = array_filter($entityKeys, function ($key) use ($dataKeys) {
            return in_array($key, $dataKeys);
        });

        if (count($filter) === count($data)) {
            return true;
        }

        throw new OrmException('Data ' . print_r($data, true) . ' not valid for model ' . $entity);
    }

    /**
     * Find a group of documents and return them as an array
     *
     * @param string $entity
     * @param array $query
     * @return array
     */
    public function findAsArray(string $entity, array $query = []): array
    {
        return $this->client->getCollection($entity::getTable())->find($this->makeObjectId($query))->toArray();
    }

    /**
     * Find a single document and return it as an array
     *
     * @param string $entity
     * @param array $query
     * @return array
     */
    public function findOneAsArray(string $entity, array $query = []): array
    {
        $result = $this->findAsArray($entity, $query);

        if (count($result) > 0) {
            return $this->mongoToEntity($result[0]);
        }

        return [];
    }

    /**
     * Insert a single entity model into mongo
     *
     * @param BaseModel $entity
     * @return InsertOneResult
     */
    public function insertOne(BaseModel $entity): InsertOneResult
    {
        $collection = $this->client->getCollection($entity::getTable());

        $data = $entity->extract();

        unset($data['id']);

        return $collection->insertOne($data);
    }
}
