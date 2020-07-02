<?php
namespace Api\Mapper;

use Api\Entity\CollectionInterface;
use Api\Entity\EntityInterface;

interface MapperInterface
{
    public function fetchById(string $id, bool $withDeleted = false) : ?EntityInterface;

    public function fetchOneBy(array $conditions = [], bool $withDeleted = false) : ?EntityInterface;

    public function fetchAllBy(
        array $conditions = [],
        bool $withDeleted = false,
        array $options = []
    ) : CollectionInterface;

    public function countAllBy(array $conditions = [], bool $withDeleted = false) : int;

    public function insert(EntityInterface $entity);

    public function update(EntityInterface $entity, array $set);

    public function delete(EntityInterface $entity);

    public function remove(EntityInterface $entity);

    public function createEntity() : EntityInterface;
}
