<?php


namespace App\Model\Database\EAV;


use App\Model\Database\EAV\Data\UpdateData;
use App\Model\Database\EAV\Exceptions\EntityNotFoundException;
use App\Model\Database\EAV\Objects\EAV_Entity;
use App\Model\Database\Repository\Dynamic\AttributeRepository;
use App\Model\Database\Repository\Dynamic\Entity\DynamicEntity;
use App\Model\Database\Repository\Dynamic\EntityRepository;
use App\Model\Database\Repository\Dynamic\IdRepository;
use App\Model\Database\Repository\Dynamic\ValueRepository;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Utils\FileSystem;

class EAVRepository
{
    /**
     * @var ActiveRow|DynamicEntity|null
     */
    private ActiveRow|DynamicEntity|null $entity;

    /**
     * @throws EntityNotFoundException
     */
    public function __construct(private Explorer $explorer, public AttributeRepository $attributeRepository, public EntityRepository $entityRepository, public IdRepository $idRepository,
                                public ValueRepository $valueRepository, public string $table) {
        $this->entity = $this->entityRepository->findByColumn(DynamicEntity::name, $table)->fetch();
        if(!$this->entity) throw new EntityNotFoundException();
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
     * @param int $uniqueId
     * @return array (associative: [attr => value])
     */
    public function findByUnique(int $uniqueId): array {
         $sql = FileSystem::read("SQL/findByUnique.sql");
         $rows = $this->explorer->query($sql, $this->entity->id, $uniqueId)->fetchAll();
         return self::createAssociativeArray($rows)[$this->entity->id];
    }

    /**
     * @param int $uniqueId
     * @param array $data
     * @return array (info about updated rows [attr => bool(updated)])
     * TODO: check problem with updating value that isn't inserted
     */
    public function updateByUnique(int $uniqueId, array $data): array
    {
        $sql = FileSystem::read("SQL/updateByUnique.sql");
        $result = [];
        foreach ($data as $k => $v) {
            $result[$k] = $this->explorer->query($sql, $v, $k);
        }
        return $result;
    }

    /**
     * @return array ([<row_unique> => [], <row_unique> => []])
     */
    public function findAll(): array {
        $sql = FileSystem::read("SQL/findAll.sql");
        $rows = $this->explorer->query($sql, $this->entity->id)->fetchAll();
        $result = [];
        foreach ($rows as $row) $row[$row->row_unique] = self::createAssociativeArray($rows)[$this->entity->id];
        return $result;
    }

    public function insert(array $data) {

    }

    public function deleteById(int $id) {

    }
}