<?php
namespace Api\Entity;

use LosMiddleware\ApiServer\Entity\Entity;

abstract class AbstractEntity extends Entity implements EntityInterface
{
    public function __construct()
    {
        $now = date('Y-m-d H:i:s');
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    /**
     * @return string
     */
    public function id(): string
    {
        return $this->id;
    }

    public function exchangeArray(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            } elseif (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

    public function getArrayCopy() : array
    {

        if (empty($this->fields)) {
            $fields = get_object_vars($this);
            unset($fields['inputFilter']);
            unset($fields['fields']);
            $this->fields = array_keys($fields);
        }

        $list = [];
        foreach ($this->fields as $field) {
            $method = 'get'.ucfirst($field);
            if (method_exists($this, $method)) {
                $list[$field] = $this->$method();
            } elseif (property_exists($this, $field)) {
                $list[$field] = $this->$field;
            }
        }

        if (! array_key_exists('deleted', $list) || $list['deleted'] !== true) {
            $list['deleted'] = false;
            $list['deletedAt'] = null;
        }
        return $list;
    }
}
