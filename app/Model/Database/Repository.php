<?php

namespace App\Model\Database;

use App\Model\Database\Repository\Common\Entity\SoftDeleteObject;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Traversable;

/**
 * Class Repository
 * @package App\Model\Database
 */
abstract class Repository implements IRepository
{
    protected string $table;
    protected Explorer $explorer;

    /**
     * AbstractRepository constructor.
     * @param string $table In elastic search type == table of MySQL
     */
    public function __construct(string $table, Explorer $explorer) {
        $this->table = $table;
        $this->explorer = $explorer;
    }

    /**
     * @return Selection
     */
    public function table(): Selection
    {
        return $this->explorer->table($this->table);
    }

    /**
     * @return string
     */
    public function getTable(): string {
        return $this->table;
    }

    /**
     * @return Explorer
     */
    public function getExplorer(): Explorer {
        return $this->explorer;
    }

    /**
     * @param string $timeColumn
     * @return Selection
     */
    public function findTodayRows(string $timeColumn): Selection {
        return $this->findAll()->where($timeColumn." >= CURDATE() AND ".$timeColumn." < CURDATE() + INTERVAL 1 DAY");
    }

    /**
     * @param int $id
     * @param array|null $select
     * @param bool $includesPrivate
     * @return ActiveRow|null
     */
    public function findById(int $id, ?array $select = null, bool $includesPrivate = false): ?ActiveRow
    {
        //if(!$select) $select = $this->getAllowedValues();
        $table = $this->explorer->table($this->table)->wherePrimary($id);
        //if(!$includesPrivate /*&& $this->hasPrivateColumns()*/) $table->where("private = ?", 0);
        return $select ? $table->select($select)->fetch() : $table->fetch();
    }

    /**
     * @param string $column
     * @param string $value
     * @param array|null $select
     * @param bool $includesDeleted
     * @return Selection
     */
    public function findByColumn(string $column, string $value, ?array $select = null, bool $includesDeleted = false): Selection
    {
        //if(!$select) $select = $this->getAllowedValues();
        $table = $this->explorer->table($this->table);
        if(property_exists(DataStructure::ENTITIES[$this->table], SoftDeleteObject::deleted)) { // test
            if(!$includesDeleted) {
                $table->where("deleted = ?", 0);
            }
        }
        if($select) $table->select($select);
        return $table->where($column . " = ?", $value);
    }

    /**
     * @param iterable $data
     * @return int
     */
    public function update(iterable $data): int {
        return $this->table()->update($data);
    }

    /**
     * @param string $condition
     * @param array $params
     * @param iterable $data
     * @return int
     */
    public function updateByColumn(string $condition, array $params, iterable $data): int {
        return $this->table()->where($condition, ...$params)->update($data);
    }

    /**
     * @param int|string $id
     * @param iterable $data
     * @return array
     */
    public function updateById(int|string $id, iterable $data): array
    {
        return [$this->explorer->table($this->table)->wherePrimary($id)->update($data)];
    }

    /**
     * @param array $allRows
     * @param string $keyColumn
     * @param string|null $valueColumn
     * @return array
     */
    public static function generateMap(array $allRows, string $keyColumn, ?string $valueColumn = null): array
    {
        $map = [];
        foreach ($allRows as $row) $map[$row->{$keyColumn}] = !$valueColumn ? $row : $row->{$valueColumn};
        return $map;
    }

    /**
     * @param string|null $orderQuery
     * @param array|null $select
     * @param bool $includesDeleted
     * @return Selection
     */
    public function findAll(?string $orderQuery = null, ?array $select = null, bool $includesDeleted = false): Selection {
        //if(!$select) $select = $this->getAllowedValues();
        $table = $this->explorer->table($this->table);
        if(property_exists(DataStructure::ENTITIES[$this->table], SoftDeleteObject::deleted)) { // test
            if(!$includesDeleted) {
                $table->where("deleted = ?", 0);
            }
        }
        //if(!$includesPrivate && $this->hasPrivateColumns()) $table->where("private = ?", 0);
        $orderBuild = $orderQuery ? $table->order($orderQuery) : $table;
        return $orderBuild->select($select ?: "*");
    }

    /**
     * @param iterable $data
     * @return int|bool|ActiveRow|Selection|iterable
     */
    public function insert(iterable $data): int|bool|ActiveRow|Selection|iterable
    { // TODO: return type
        return $this->explorer->table($this->table)->insert($data);
    }

    /**
     * ATTENTION: to softDelete and normal delete
     * @param int|string $id
     * @return int
     */
    public function deleteById(int|string $id): int {
        if(property_exists(DataStructure::ENTITIES[$this->table], SoftDeleteObject::deleted)) {
            return $this->updateById($id, [
                SoftDeleteObject::deleted => 1
            ])[0];
        }
        return $this->explorer->table($this->table)->wherePrimary($id)->delete();
    }

    /**
     * @param string $column
     * @param $value
     * @return int
     */
    public function deleteByColumn(string $column, $value): int {
        return $this->explorer->table($this->table)->where($column . " = ?", $value)->delete();
    }

    /**
     * @return int
     */
    public function getCount(): int {
        return $this->explorer->table($this->table)->count("*");
    }
}