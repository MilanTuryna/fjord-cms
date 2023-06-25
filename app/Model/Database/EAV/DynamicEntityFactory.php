<?php


namespace App\Model\Database\EAV;


use App\Model\Database\EAV\Exceptions\EntityNotFoundException;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\Dynamic\IdRepository;
use App\Model\Database\Repository\Dynamic\ValueRepository;
use InvalidArgumentException;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\UniqueConstraintViolationException;
use ReflectionException;

class DynamicEntityFactory
{

    /**
     * DynamicEntityFactory constructor.
     * @param Explorer $explorer
     * @param EntityRepository $entityRepository
     * @param AttributeRepository $attributeRepository
     * @param IdRepository $idRepository
     * @param ValueRepository $valueRepository
     */
    public function __construct(private Explorer $explorer, private EntityRepository $entityRepository,
                                private AttributeRepository $attributeRepository, private IdRepository $idRepository, private ValueRepository $valueRepository
    )
    {
    }

    /**
     * @param DynamicEntity $entity
     * @param array $dynamicAttributes
     * @return int|null
     * @throws ReflectionException
     * @throws UniqueConstraintViolationException
     */
    public function createEntity(DynamicEntity $entity, array $dynamicAttributes): ?int
    {
            $insertedEntity = $this->entityRepository->insert($entity->iterable());
            foreach ($dynamicAttributes as $attribute) {
                if (!($attribute instanceof DynamicAttribute || is_array($attribute))) {
                    throw new InvalidArgumentException("Zadaný atribut je špatného typu.");
                }
                $attributeObject = new DynamicAttribute();
                $attributeObject->createFrom($attribute);
                $this->attributeRepository->addAttribute($insertedEntity->id, $attribute);
            }
            return $insertedEntity->{'id'} ?? null;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isEntityExist(string $name): bool {
        return (bool)$this->entityRepository->findByColumn(DynamicEntity::name, $name)->fetch();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getEntityRepository(string $entityName): EAVRepository
    {
        return new EAVRepository($this->explorer, $this->attributeRepository, $this->entityRepository, $this->idRepository, $this->valueRepository, $entityName);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getEntityRepositoryById(int $id): EAVRepository {
        $entity = $this->entityRepository->findById($id);
        return $this->getEntityRepository($entity->name);
    }
}