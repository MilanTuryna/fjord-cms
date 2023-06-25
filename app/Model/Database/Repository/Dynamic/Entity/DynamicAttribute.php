<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicAttribute
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicAttribute extends Entity
{
    const id_name = "id_name";
    const data_type = "data_type", input_type = "input_type", title = "title", placeholder = "placeholder", preset_value = "preset_value", entity_id = "entity_id",
       description = "description",
        enabled_wysiwyg = "enabled_wysiwyg",
        generate_value = "generate_value",
        allowed_translation = "allowed_translation", required = "required";

    public string $id_name;
    public string $title;
    public string $description;
    public bool $allowed_translation;
    public string $input_type;
    public string $data_type;
    public string $placeholder;
    public string $generate_value; // ENUM (GeneratedValues)
    public string $preset_value;
    public bool $enabled_wysiwyg;
    public bool $required;
    public int $entity_id;
    public int $id;
}