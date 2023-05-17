<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicId
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicId extends Entity
{
    const name = "name";
    const created = "created";
    const description = "description";
    const id = "id";

    public string $name;
    public string $created;
    public string $description;
    public int $id;
}