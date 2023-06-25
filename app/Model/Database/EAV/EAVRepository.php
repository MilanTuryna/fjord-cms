<?php


namespace App\Model\Database\EAV;


use App\Forms\Dynamic\Data\AttributeData;
use App\Forms\Dynamic\Data\GeneratedValues;
use App\Forms\Dynamic\Enum\InputType;
use App\Model\Cryptography;
use App\Model\Database\EAV\Exceptions\EntityNotFoundException;
use App\Model\Database\EAV\Exceptions\InvalidAttributeException;
use App\Model\Database\EAV\Translations\TranslatedValue;
use App\Model\Database\IRepository;
use App\Model\Database\Repository\Admin\Entity\Account;
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
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\FileSystem;
use Nette\Utils\JsonException;

class EAVRepository implements IRepository
{
    /**
     * @var ActiveRow|DynamicEntity|null
     */
    private ActiveRow|DynamicEntity|null $entity;
    private int|null $admin_id;

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
        foreach ($rows as $row) $result[$row->attribute] = is_array($row->value) ? ArrayHash::from($row->value) : $row->value;
        return $result;
    }

    /**
     * @param string $column
     * @param mixed $data
     * @return mixed (associative: [attr => value])
     */
    public function findByColumn(string $column, mixed $data): mixed
    {
        $sql = FileSystem::read(__DIR__ . "/SQL/findByColumn.sql");
        $sql = sprintf($sql, $column); // SQL injection vulnerable
        $rows = $this->explorer->query($sql, $this->entity->id, $data)->fetchAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row["row_unique"]][$row->attribute] = $row->value;
            $result[$row["row_unique"]]["row_unique"] = $row->row_unique;
        }
        return ArrayHash::from($result);
    }

    /**
     * @param string $uniqueId
     * @return ArrayHash (associative: [attr => value])
     */
    public function findByUnique(string $uniqueId): ArrayHash {
        return $this->findByColumn(DynamicId::row_unique, $uniqueId)[$uniqueId];
    }

    /**
     * @param int|string $id
     * @param array $data
     * @return array (info about updated rows [attr => bool(updated)])
     * TODO: check problem with updating value that isn't inserted
     */
    public function updateById(int|string $id, iterable $data): array
    {
        $sql = FileSystem::read(__DIR__ . "/SQL/updateByUnique.sql");
        $result = [];
        foreach ($data as $k => $v) {
            $result[$k] = (bool)$this->explorer->query($sql, $v, $k, $id)->getRowCount();
        }
        return $result;
    }

    /**
     * @return array ([<row_unique> => [], <row_unique> => []])
     * @throws JsonException
     */
    public function findAll(): array {
        $sql = FileSystem::read(__DIR__ . "/SQL/findAll.sql");
        $rows = $this->explorer->query($sql, $this->entity->id)->fetchAll();
        $attributeAssoc = $this->getEntityAttributesAssoc();
        $result = [];
        foreach ($rows as $row) {
            bdump($row);
            $attribute = $attributeAssoc[$row->attribute];
            $translation = ($attribute[DynamicAttribute::allowed_translation] || $attribute[DynamicAttribute::data_type] === TranslatedValue::class)
                && !$attribute[DynamicAttribute::generate_value];
            $dateTime = in_array($attribute[DynamicAttribute::generate_value], [GeneratedValues::CREATED, GeneratedValues::EDITED])
                || $attribute[DynamicAttribute::data_type] === DateTime::class || $attribute[DynamicAttribute::input_type] === InputType::DATE_INPUT;
            if($translation) {
                $translatedValue = new TranslatedValue([]);
                $translatedValue->importJson($row->value);
                $result[$row->row_unique][$row->attribute] = $translatedValue;
            } elseif($dateTime) {
                try {
                    $result[$row->row_unique][$row->attribute] = DateTime::from($row->value);
                } catch (Exception $e) {
                    continue;
                }
            } else {
                $result[$row->row_unique][$row->attribute] = $row->value;
            }
            $result[$row->row_unique]["row_unique"] = $row->row_unique;
        }
        bdump($result);
        return $result;
    }

    /**
     * @param array $data
     * @return array
     * @throws InvalidAttributeException
     * @throws Exception
     */
    public function insert(iterable $data): array {
        bdump($data);
        $attributes = $this->getEntityAttributesAssoc();
        $result = [];
        $newDynamicID = $this->idRepository->insert([
            DynamicId::row_unique => Cryptography::createUnique(),
            DynamicId::entity_id => $this->entity->id,
            DynamicId::created  => new \DateTime(),
        ]);
        try {
            foreach ($data as $attr => $v) {
                if(!isset($attributes[$attr])) {
                    throw new InvalidAttributeException("Attribute '" . $attr . "' passed in insert data doesn't exist.");
                }
                $attrId = $attributes[$attr]['id'];
                $insertData = [
                    DynamicValue::entity_id  => $this->entity->id,
                    DynamicValue::attribute_id => $attrId,
                    DynamicValue::row_id => $newDynamicID->{'id'},
                    DynamicValue::value => $v
                ];
                $sqlQuery = $this->valueRepository->insert($insertData);
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