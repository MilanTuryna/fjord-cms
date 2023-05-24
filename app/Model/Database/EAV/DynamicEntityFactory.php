<?php


namespace App\Model\Database\EAV;


use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use InvalidArgumentException;
use Nette\Database\Table\ActiveRow;

class DynamicEntityFactory
{
    private EntityRepository$entityRepository;
    private AttributeRepository$attributeRepository;

    /**
     * DynamicEntityFactory constructor.
     * @param EntityRepository $entityRepository
     * @param AttributeRepository $attributeRepository
     */
    public function __construct(EntityRepository $entityRepository, AttributeRepository $attributeRepository)
    {
        $this->entityRepository = $entityRepository;
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * @param DynamicEntity $entity
     * @param array $dynamicAttributes
     * @return int|null
     */
    public function createEntity(DynamicEntity $entity, array $dynamicAttributes): ?int
    {
        $insertedEntity = $this->entityRepository->insert($entity->iterable());
        foreach ($dynamicAttributes as $attribute) {
            if (!($attribute instanceof DynamicAttribute || is_array($attribute))) {
                throw new InvalidArgumentException("Zadaný atribut je špatného typu.");
            }
            $this->attributeRepository->addAttribute($insertedEntity->id, $attribute);
        }
        return $insertedEntity->{'id'} ?? null;
    }
}