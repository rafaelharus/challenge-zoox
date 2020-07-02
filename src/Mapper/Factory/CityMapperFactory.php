<?php
namespace Api\Mapper\Factory;

use Api\Entity\CityCollection;
use Api\Entity\CityEntity;
use Api\Mapper\CityMapper;
use Psr\Container\ContainerInterface;
use MongoDB\Client;

class CityMapperFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $db = $container->get('config')['db-mongo']['uri'];
        $client = new Client($db);
        return new CityMapper(
            $client->selectCollection('region', 'city'),
            CityEntity::class,
            CityCollection::class
        );
    }
}
