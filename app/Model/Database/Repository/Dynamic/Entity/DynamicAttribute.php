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
    const data_type = "data_type", placeholder = "placeholder", preset_val = "preset_val", entity_id = "entity_id",
       description = "description",
        generate_value = "generate_value",
        allowed_translation = "allowed_translation", required = "required";

    public string $name;
    public string $description;
    public bool $allowed_translation;
    public string $data_type;
    public string $placeholder;
    public string $generate_value; // ENUM
    public string $preset_value;
    public bool $required;
    public int $entity_id;
    public int $id;
}