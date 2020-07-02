<?php
namespace Api\Mapper\Factory;

use Api\Entity\StateCollection;
use Api\Entity\StateEntity;
use Api\Mapper\StateMapper;
use Psr\Container\ContainerInterface;
use MongoDB\Client;

class StateMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $db = $container->get('config')['db-mongo']['uri'];
        $client = new Client($db);
        return new StateMapper(
            $client->selectCollection('region', 'state'),
            StateEntity::class,
            StateCollection::class
        );
    }
}
