<?php

namespace Api\Entity;

use Laminas\InputFilter\InputFilterAwareInterface;
use Laminas\Stdlib\ArraySerializableInterface;

interface EntityInterface extends ArraySerializableInterface, InputFilterAwareInterface
{
    public function id(): string;
}
