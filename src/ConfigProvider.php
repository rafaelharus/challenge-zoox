<?php

declare(strict_types=1);

namespace Api;

use Api\Entity\CityCollection;
use Api\Entity\CityEntity;
use Api\Entity\StateCollection;
use Api\Entity\StateEntity;
use Api\Middleware\AuthMiddleware;
use Api\Middleware\AuthMiddlewareFactory;
use Laminas\Hydrator\ArraySerializable as ArraySerializableHydrator;
use Mezzio\Hal\Metadata\MetadataMap;
use Mezzio\Hal\Metadata\RouteBasedCollectionMetadata;
use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;
use Mezzio\Helper;

class ConfigProvider
{
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            MetadataMap::class => $this->getMetadataMap(),
        ];
    }

    public function getDependencies() : array
    {
        return [
            'invokables' => [
                Helper\ServerUrlHelper::class => Helper\ServerUrlHelper::class,
            ],
            'factories'  => [
                AuthMiddleware::class => AuthMiddlewareFactory::class,
                Mapper\StateMapper::class => Mapper\Factory\StateMapperFactory::class,
                Mapper\CityMapper::class => Mapper\Factory\CityMapperFactory::class,
                Handler\StateHandler::class => Handler\StateHandlerFactory::class,
                Handler\CityHandler::class => Handler\CityHandlerFactory::class,
            ],
        ];
    }

    public function getMetadataMap() : array
    {
        return [
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => StateCollection::class,
                'collection_relation' => 'states',
                'route' => 'api.state.collection',
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => StateEntity::class,
                'route' => 'api.state.entity',
                'extractor' => ArraySerializableHydrator::class,
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => CityCollection::class,
                'collection_relation' => 'cities',
                'route' => 'api.city.collection',
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => CityEntity::class,
                'route' => 'api.city.entity',
                'extractor' => ArraySerializableHydrator::class,
            ],
        ];
    }
}
