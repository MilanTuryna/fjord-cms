<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicEntity
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicEntity extends Entity
{
    const name = "name", created = "created", description = "description", last_edit = "last_edit", id = "id";

    public string $name;
    public string $created;
    public string $description;
    public string $last_edit;
    public int $id;
}