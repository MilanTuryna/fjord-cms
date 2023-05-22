<?php


namespace App\Forms\Dynamic\Data;


use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;

/**
 * Class EntityFormData
 * @package App\Forms\Dynamic\Data
 */
class EntityFormData
{
    public string $entity_name;
    public string $entity_description;
    /**
     * @var array|DynamicAttribute[]
     */
    public array $attributes;
}