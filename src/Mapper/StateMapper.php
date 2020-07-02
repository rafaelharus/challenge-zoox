<?php
namespace Api\Mapper;

use Laminas\Cache\Storage\StorageInterface;
use MongoDB\Collection;

class StateMapper extends AbstractMapper
{
    private $cache;

    public function __construct(
        Collection $collection,
        string $entityClass,
        string $collectionClass,
        StorageInterface $cache
    ) {
        parent::__construct($collection, $entityClass, $collectionClass);
        $this->cache = $cache;
    }

    public function fetchAllByCached() : array
    {
        $methodName = str_replace(['\\', '::'], '_', strtolower(__METHOD__));
        $cacheKey = md5($methodName);
        if ($this->cache->hasItem($cacheKey) !== false) {
            return json_decode($this->cache->getItem($cacheKey), true);
        }

        $list = [];
        $result = $this->collection->find([], []);
        if ($result !== null) {
            $list = $result->toArray();
        }

        $data = [];
        foreach ($list as $item) {
            $state = $item->getArrayCopy();
            $data[$state['_id']] = $state['uf'];
        }

        $this->cache->setItem($cacheKey, json_encode($data));
        return $data;
    }
}
