<?php


namespace App\Model\Database\EAV\Objects;


use App\Model\Database\EAV\Translations\TranslatedValue;
use App\Model\Database\Entity;

/**
 * Class EAV_Entity
 * @package App\Model\Database\EAV\Objects
 */
class EAV_Entity extends Entity
{
    const entity_id = "entity_id";
    const row_unique = "row_unique";
    const value = "value";
    const attribute = "attribute";
    const data_type = "data_type";
    const id = "id";

    public int $row_unique;
    public int $entity_id;
    public string $value;
    public string $data_type;
    public string $attribute;
}