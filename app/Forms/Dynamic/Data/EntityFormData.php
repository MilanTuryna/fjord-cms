<?php


namespace App\Forms\Dynamic\Data;


use App\Model\Database\Entity;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use ReflectionException;

/**
 * Class EntityFormData
 * @package App\Forms\Dynamic\Data
 */
class EntityFormData extends Entity
{
    // used when editing multiplier form (use CREATE on new multiplier blocks and update on old)
    const ROW_KEY_CHAR = "E";

    public string $entity_name;
    public string $entity_description;
    public string $menu_item_name;
    /**
     * @var array|DynamicAttribute[]
     */
    public ?array $attributes;

    /**
     */
    public static function generateFullEntity(int $entity_id, EntityRepository $entityRepository, AttributeRepository $attributeRepository): EntityFormData {
        /**
         * @var $entityRow DynamicEntity
         */
        $fullEntity = new self();
        $entityRow = $entityRepository->findById($entity_id);
        $fullEntity->entity_name = $entityRow->name;
        $fullEntity->entity_description = $entityRow->description;
        $fullEntity->menu_item_name = $entityRow->menu_item_name;
        $attributes = $attributeRepository->findByColumn("entity_id", $entity_id)->select("*")->fetchAll();
        $fullEntity->attributes = [];
        foreach ($attributes as $attribute) {
            $fullEntity->attributes[self::ROW_KEY_CHAR.$attribute->{'id'}] = $attribute->toArray();
        }
        return $fullEntity;
    }
}