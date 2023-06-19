<?php


namespace App\Model\Database\EAV;


use App\Model\Cryptography;
use App\Model\Database\EAV\Exceptions\EntityNotFoundException;
use App\Model\Database\EAV\Exceptions\InvalidAttributeException;
use App\Model\Database\IRepository;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicAttribute;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\Entity\DynamicId;
use App\Model\Database\Repository\Dynamic\Entity\DynamicValue;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\Dynamic\IdRepository;
use App\Model\Database\Repository\Dynamic\ValueRepository;
use Exception;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\FileSystem;

class EAVRepository implements IRepository
{
    /**
     * @var ActiveRow|DynamicEntity|null
     */
    private ActiveRow|DynamicEntity|null $entity;

    /**
     * @throws EntityNotFoundException
     */
    public function __construct(private Explorer $explorer, public AttributeRepository $attributeRepository,
                                public EntityRepository $entityRepository, public IdRepository $idRepository,
                                public ValueRepository $valueRepository, public string $entityName) {
        $this->entity = $this->entityRepository->findByColumn(DynamicEntity::name, $entityName)->fetch();
        if(!$this->entity) throw new EntityNotFoundException();
    }

    /**
     * @param string $path
     * @return array
     */
    public function getEntityAttributesAssoc(string $path = DynamicAttribute::id_name): array {
        return $this->attributeRepository->findByColumn( DynamicAttribute::entity_id, $this->entity->id)->fetchAssoc($path);
    }

    /**
     * @param array $rows
     * @return array
     */
    public function createAssociativeArray(array $rows): array {
        $result = [];
        foreach ($rows as $row) $result[$row->attribute] = $row->value;
        return $result;
    }

    /**
     * @param string $column
     * @param mixed $data
     * @return mixed (associative: [attr => value])
     */
    public function findByColumn(string $column, mixed $data): mixed
    {
        $sql = FileSystem::read("SQL/findByColumn.sql");
        $rows = $this->explorer->query($sql, $this->entity->id, $column, $data)->fetchAll();
        return self::createAssociativeArray($rows)[$this->entity->id];
    }

    /**
     * @param string $uniqueId
     * @return array (associative: [attr => value])
     */
    public function findByUnique(string $uniqueId): array {
        return $this->findByColumn(DynamicId::row_unique, $uniqueId);
    }

    /**
     * @param int $id
     * @param array $data
     * @return array (info about updated rows [attr => bool(updated)])
     * TODO: check problem with updating value that isn't inserted
     */
    public function updateById(int $id, iterable $data): array
    {
        $sql = FileSystem::read("SQL/updateByUnique.sql");
        $result = [];
        foreach ($data as $k => $v) {
            $result[$k] = (bool)$this->explorer->query($sql, $v, $k)->getRowCount();
        }
        return $result;
    }

    /**
     * @return array ([<row_unique> => [], <row_unique> => []])
     */
    public function findAll(): array {
        $sql = FileSystem::read(__DIR__ . "/SQL/findAll.sql");
        $rows = $this->explorer->query($sql, $this->entity->id)->fetchAll();
        $associativeArray = self::createAssociativeArray($rows);
        bdump($associativeArray);
        bdump($rows);
        $result = [];
        foreach ($rows as $row) $row[$row->row_unique] = $associativeArray[$this->entity->id];
        return $result;
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidAttributeException
     * @throws Exception
     */
    public function insert(iterable $data): array {
        $attributes = $this->getEntityAttributesAssoc();
        $result = [];
        $newDynamicID = $this->idRepository->insert([
            DynamicId::row_unique => Cryptography::createUnique(),
            DynamicId::entity_id => $this->entity->id,
            DynamicId::created => new \DateTime(),
        ]);
        try {
            foreach ($data as $attr => $v) {
                if(!isset($attributes[$attr])) {
                    throw new InvalidAttributeException("Attribute '" . $attr . "' passed in insert data doesn't exist.");
                }
                $attrId = $attributes[$attr]['id'];
                $sqlQuery = $this->valueRepository->insert([
                    DynamicValue::entity_id  => $this->entity->id,
                    DynamicValue::attribute_id => $attrId,
                    DynamicValue::row_id => $newDynamicID->{'id'}
                ]);
                $result[$attr] = $sqlQuery;
            }
        } catch (Exception $exception) {
            if($newDynamicID) $this->idRepository->deleteById($newDynamicID->{'id'}); // delete id if it was bad
            throw $exception;
        }
        return $result;
    }

    public function deleteById(int $id): int
    {
        return $this->idRepository->deleteByColumn(DynamicId::row_unique, $id); // TODO: set foreign keys for delete all values for t
    }
}