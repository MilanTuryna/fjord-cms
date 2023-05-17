<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicAttribute
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicAttribute extends Entity
{
    const name = "name";
    const data_type = "data_type", placeholder_expression_id = "placeholder_expression_id", preset_val = "preset_val", entity_id = "entity_id";

    public string $name;
    public string $data_type;
    public int $placeholder_expression_id;
    public int $entity_id;
    public string $preset_val;
    public int $id;
}