<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicValue
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicValue extends Entity
{
    const entity_id = "entity_id", attribute_id = "attribute_id", value_expression_id = "value_expression_id";

    public string $entity_id;
    public string $attribute_id;
    public string $value_expression_id;
}