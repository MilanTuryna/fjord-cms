<?php


namespace App\Model\Database\EAV;


use App\Model\Database\EAV\Exceptions\EntityNotFoundException;
use App\Model\Database\EAV\Exceptions\InvalidAttributeException;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\Dynamic\IdRepository;
use App\Model\Database\Repository\Dynamic\ValueRepository;
use App\Utils\FormatUtils;
use InvalidArgumentException;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Schema\DynamicParameter;
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
     * @throws InvalidAttributeException
     */
    public function createEntity(DynamicEntity $entity, array $dynamicAttributes): ?int
    {
            $insertedEntity = $this->entityRepository->insert($entity->iterable());
            foreach ($dynamicAttributes as $attribute) {
                if (!($attribute instanceof DynamicAttribute || is_array($attribute))) {
                    throw new InvalidArgumentException("Zadaný atribut je špatného typu.");
                }
                if(!FormatUtils::validateInputName($attribute[DynamicAttribute::id_name])) {
                    throw new InvalidAttributeException("Jmenný identifikátor u každého atributu musí být bez diakritiky, bez mezer a malými písmeny. Zkontrolujte: '" . $attribute[DynamicAttribute::id_name] . "'");
                }
                $attributeObject = new DynamicAttribute();
                $attribute[DynamicAttribute::enabled_wysiwyg] = isset($attribute[DynamicAttribute::enabled_wysiwyg]);
                $attribute[DynamicAttribute::required] = isset($attribute[DynamicAttribute::required]);
                $attributeObject->createFrom((object)$attribute);
                $this->attributeRepository->addAttribute($insertedEntity->id, $attributeObject);
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

    public function getEntitiesSchema(): array {
        $dynEntities = $this->entityRepository->findAll()->fetchAll();
        $result = [];
        foreach ($dynEntities as $entity) {
            $insideResult =[
                "entity_name" => $entity->name,
                "entity_description" => $entity->description,
                "entity_menu_item_name" => $entity->menu_item_name
            ];
            $attributes = $this->attributeRepository->findByColumn(DynamicAttribute::entity_id, $entity->id)->fetchAll();
            foreach ($attributes as $attribute) {
                if(!isset($dynArray["attributes"])) $dynArray["attributes"] = [];
                $arr = $attribute->toArray();
                unset($arr["id"]);
                unset($arr["entity_id"]);
                $insideResult["attributes"][] = $arr;
            }
            $result[] = $insideResult;
        }
        return $result;
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