<?php
namespace Api\Mapper;

use Api\Entity\CollectionInterface;
use Api\Entity\EntityInterface;
use MongoDB\Collection;
use Laminas\Paginator\Adapter\ArrayAdapter;

abstract class AbstractMapper implements MapperInterface
{
    protected $collection;
    private $entityClass;
    private $collectionClass;

    public function __construct(Collection $collection, string $entityClass, string $collectionClass)
    {
        $this->collection = $collection;
        $this->entityClass = $entityClass;
        $this->collectionClass = $collectionClass;
    }

    public function fetchById(string $id, bool $withDeleted = false) : ?EntityInterface
    {
        $query = ['_id' => $id];
        if ($withDeleted) {
            $query['deleted'] = ['$ne' => true];
        }
        return $this->fetchOneBy(['_id' => $id]);
    }

    public function fetchOneBy(array $conditions = [], bool $withDeleted = false) : ?EntityInterface
    {
        if ($withDeleted) {
            $conditions['deleted'] = ['$ne' => true];
        }
        $result = $this->collection->findOne($conditions);
        if ($result === null) {
            return null;
        }
        return $this->createFromStorage($result->getArrayCopy());
    }

    public function fetchAllBy(
        array $conditions = [],
        bool $withDeleted = false,
        array $options = []
    ) : CollectionInterface {
        if ($withDeleted) {
            $conditions['deleted'] = ['$ne' => true];
        }
        $list = [];
        $result = $this->collection->find($conditions, $options);
        if ($result !== null) {
            $list = $result->toArray();
        }
        return $this->createCollectionFromStorage($list);
    }

    public function countAllBy(array $conditions = [], bool $withDeleted = false) : int
    {
        if ($withDeleted) {
            $conditions['deleted'] = ['$ne' => true];
        }
        $result = $this->collection->find($conditions);
        return count($result->toArray());
    }

    public function insert(EntityInterface $entity)
    {
        $data = $this->extractDataForStorage($entity);
        $data['createdAt'] = date('c');
        $data['updatedAt'] = $data['createdAt'];
        $this->collection->insertOne($data);
        $entity->exchangeArray($data);
    }

    public function update(EntityInterface $entity, array $set)
    {
        $set['updatedAt'] = date('c');
        $this->collection->updateOne(['_id' => $entity->id()], ['$set' => $set]);
        $entity->exchangeArray($set);
    }

    public function delete(EntityInterface $entity)
    {
        $set = [];
        $set['deleted'] = true;
        $set['deletedAt'] = date('c');
        $this->collection->updateOne(['_id' => $entity->id()], ['$set' => $set]);
        $entity->exchangeArray($set);
    }

    public function remove(EntityInterface $entity)
    {
        $this->collection->deleteOne(['_id' => $entity->id()]);
    }

    public function createEntity() : EntityInterface
    {
        return new $this->entityClass;
    }

    private function extractDataForStorage(EntityInterface $entity) : array
    {
        $data = $entity->getArrayCopy();
        $data['_id'] = $data['id'];
        unset($data['id']);
        return $data;
    }

    private function createCollectionFromStorage(array $data) : CollectionInterface
    {
        $list = [];
        foreach ($data as $planData) {
            $list[] = $this->createFromStorage($planData->getArrayCopy());
        }
        return new $this->collectionClass(new ArrayAdapter($list));
    }

    private function createFromStorage(array $data) : EntityInterface
    {
        /** @var EntityInterface $entity */
        $entity = new $this->entityClass;
        $data['id'] = $data['_id'];
        $entity->exchangeArray($data);
        return $entity;
    }
}
