<?php

namespace Api;

class Uuid
{
    public static function uuid4() : string
    {
        $factory = new \Ramsey\Uuid\UuidFactory();
        $generator = new \Ramsey\Uuid\Generator\CombGenerator(
            $factory->getRandomGenerator(),
            $factory->getNumberConverter()
        );
        $codec = new \Ramsey\Uuid\Codec\TimestampFirstCombCodec($factory->getUuidBuilder());
        $factory->setRandomGenerator($generator);
        $factory->setCodec($codec);
        \Ramsey\Uuid\Uuid::setFactory($factory);

        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }

    public static function isValid($uuid): bool
    {
        return \Ramsey\Uuid\Uuid::isValid($uuid);
    }
}
