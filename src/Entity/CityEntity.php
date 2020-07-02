<?php
namespace Api\Entity;

use Laminas\Filter;
use Laminas\InputFilter\InputFilter;
use Laminas\Validator;

class CityEntity extends AbstractEntity
{
    protected $id;
    protected $name;
    protected $stateId;
    protected $createdAt;
    protected $updatedAt;

    public function getInputFilter()
    {
        if ($this->inputFilter === null) {
            $this->inputFilter = new InputFilter();
            $this->inputFilter->add([
                'required' => false,
                'validators' => [],
                'filters' => [],
                'name' => 'created',
            ]);

            $this->inputFilter->add([
                'required' => false,
                'validators' => [],
                'filters' => [],
                'name' => 'updated',
            ]);

            $this->inputFilter->add([
                'name' => 'name',
                'required' => true,
                'filters'    => [
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StripNewlines::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\StringLength::class,
                        'options' => [
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                            'max'      => 255,
                        ],
                    ],
                ],
            ]);

            $this->inputFilter->add([
                'name' => 'stateId',
                'required' => true,
                'filters'    => [
                    ['name' => Filter\StringTrim::class],
                    ['name' => Filter\StripTags::class],
                    ['name' => Filter\StripNewlines::class],
                ],
                'validators' => [
                    [
                        'name' => Validator\Uuid::class,
                    ],
                ],
            ]);
        }

        return $this->inputFilter;
    }
}
