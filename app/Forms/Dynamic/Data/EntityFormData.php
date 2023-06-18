<?php


namespace App\Forms\Dynamic\Data;


use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use ReflectionException;

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
    public ?array $attributes;

    /**
     * @throws ReflectionException
     */
    public static function generateFullEntity(int $entity_id, EntityRepository $entityRepository, AttributeRepository $attributeRepository): EntityFormData {
        /**
         * @var $entityRow DynamicEntity
         */
        $fullEntity = new self();
        $entityRow = $entityRepository->findById($entity_id);
        $fullEntity->entity_name = $entityRow->name;
        $fullEntity->entity_description = $entityRow->description;
        $attributes = $attributeRepository->findByColumn("entity_id", $entity_id)->fetchAll();
        $fullEntity->attributes = [];
        foreach ($attributes as $attribute) {
            $d_attribute = new DynamicAttribute();
            $d_attribute->createFrom($attribute);
            $fullEntity->attributes[$attribute->{'id'}] = $d_attribute;
        }
        return $fullEntity;
    }
}