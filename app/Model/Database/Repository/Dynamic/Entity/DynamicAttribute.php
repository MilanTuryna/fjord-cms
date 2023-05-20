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
    const data_type = "data_type", placeholder_expression_id = "placeholder_expression_id", preset_val = "preset_val", entity_id = "entity_id",
        allowed_translation = "allowed_translation";

    public string $name;
    public int $allowed_translation;
    public string $data_type;
    public string $placeholder; // json (Translated Value)
    public string $generate_value; // ENUM
    public string $preset_value;
    public bool $required;
    public int $entity_id;
    public int $id;
}