<?php


namespace App\Model\Database\Repository\Dynamic;


use App\Model\Database\Repository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use Nette\Database\Explorer;

class AttributeRepository extends Repository
{
    public function __construct(Explorer $explorer)
    {
        parent::__construct("dynamic_attribute", $explorer);
    }

    /**
     * @param int $entity_id
     * @param DynamicAttribute $attribute
     * @return mixed
     */
    public function addAttribute(int $entity_id, DynamicAttribute $attribute): mixed
    {
        $attribute->entity_id = $entity_id;
        $insertedRow = $this->table()->insert($attribute->iterable());
        return $insertedRow ? $insertedRow->id : 0;
    }
}