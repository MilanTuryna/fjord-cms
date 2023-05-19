<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicValue
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicValue extends Entity
{
    const entity_id = "entity_id", attribute_id = "attribute_id", value_expression_id = "value_expression_id", row_id = "row_id";

    public int $entity_id;
    public int $attribute_id;
    public int $row_id;
    public int $value_expression_id;
}