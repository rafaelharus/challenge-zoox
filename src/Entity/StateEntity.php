<?php
namespace Api\Entity;

use Laminas\InputFilter\InputFilter;
use Laminas\Validator;
use Laminas\Filter;

class StateEntity extends AbstractEntity implements EntityInterface
{
    protected $id;
    protected $name;
    protected $uf;
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
                'name' => 'uf',
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
                            'max'      => 2,
                        ],
                    ],
                ],
            ]);
        }

        return $this->inputFilter;
    }
}
