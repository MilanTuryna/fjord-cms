<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;
use Exception;

/**
 * Class DynamicId
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicId extends Entity
{
    const created = "created";
    const row_unique = "row_unique";
    const entity_id = "entity_id";
    const id = "id";

    public string $created;
    public int $id;
    public int $entity_id;
    public string $row_unique;
}