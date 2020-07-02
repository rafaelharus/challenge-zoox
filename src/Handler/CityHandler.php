<?php

declare(strict_types=1);

namespace Api\Handler;

use Api\Entity\CollectionInterface;
use Api\Entity\EntityInterface;
use Api\Exception\ConflictException;
use Api\Uuid;

class CityHandler extends AbstractRestHandler
{
    const IDENTIFIER_NAME = 'id';

    public function fetchAll($query = [], $options = []): CollectionInterface
    {
        $queryParams = $this->request->getQueryParams();
        $sort = $queryParams['sort'] ?? self::IDENTIFIER_NAME;
        $options['sort'] = [$sort => (int) ($queryParams['order'] ?? -1)];
        return parent::fetchAll($query, $options);
    }

    /**
     * @param array $data
     *
     * @return EntityInterface
     * @throws ConflictException
     */
    public function create(array $data) : EntityInterface
    {
        $searchName = $this->mapper->fetchOneBy(
            [
                'name' => $data['name'],
                'stateId' => $data['stateId'],
            ]
        );

        if ($searchName instanceof EntityInterface) {
            throw new ConflictException(
                'Already exists entity with this name',
                409
            );
        }

        $data['id'] = Uuid::uuid4();
        return parent::create($data);
    }

    /**
     * @param string $id
     * @param array  $data
     *
     * @return EntityInterface
     * @throws ConflictException
     */
    public function patch(string $id, array $data) : EntityInterface
    {
        $searchName = null;
        $fetch = parent::fetch($id);
        $entity = $fetch->getArrayCopy();

        if (array_key_exists('name', $data) ||
            (array_key_exists('deleted', $data) && $data['deleted'] == false)
        ) {
            $searchName = $this->mapper->fetchOneBy([
                'name' => $data['name'] ?? $entity['name'],
                'stateId' => $data['stateId'] ?? $entity['stateId'],
                '_id' => ['$ne' => $id]
            ], true);
        }

        if ($searchName instanceof EntityInterface) {
            throw new ConflictException(
                'Already exists entity with this name.',
                409
            );
        }

        return parent::patch($id, $data);
    }

    /**
     * @param string $id
     * @param array  $data
     *
     * @return EntityInterface
     * @throws ConflictException
     */
    public function update(string $id, array $data) : EntityInterface
    {
        $searchName = $this->mapper->fetchOneBy([
            'name' => $data['name'],
            'stateId' => $data['stateId'],
            '_id' => ['$ne' => $id]
        ]);

        if ($searchName instanceof EntityInterface) {
            throw new ConflictException(
                'Already exists entity with this name.',
                409
            );
        }

        return parent::update($id, $data);
    }
}
