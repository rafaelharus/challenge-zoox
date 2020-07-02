<?php
namespace Api\Mapper\Factory;

use Api\Entity\StateCollection;
use Api\Entity\StateEntity;
use Api\Mapper\StateMapper;
use Laminas\Cache\StorageFactory;
use Psr\Container\ContainerInterface;
use MongoDB\Client;

class StateMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $db = $container->get('config')['db-mongo']['uri'];
        $client = new Client($db);

        $configCache = $container->get('config')['cache']['api'];
        $cache = StorageFactory::factory($configCache);
        return new StateMapper(
            $client->selectCollection('region', 'state'),
            StateEntity::class,
            StateCollection::class,
            $cache
        );
    }
}
