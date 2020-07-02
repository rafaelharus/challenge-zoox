<?php
namespace Api\Entity;

use Countable;
use IteratorAggregate;

interface CollectionInterface extends Countable, IteratorAggregate
{
    public function setItemCountPerPage($itemCountPerPage);
    public function setCurrentPageNumber($pageNumber);
}
