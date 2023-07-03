<?php


namespace App\Model\Database\Repository\Dynamic;


use App\Model\Database\Repository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use JetBrains\PhpStorm\Pure;
use Nette\Database\Explorer;

class AttributeRepository extends Repository
{
    #[Pure] public function __construct(Explorer $explorer)
    {
        parent::__construct("fjord_dynamic_attributes", $explorer);
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