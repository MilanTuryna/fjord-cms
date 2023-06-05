<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicEntity
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicEntity extends Entity
{
    const name = "name", declension = "declension", created = "created", description = "description", last_edit = "last_edit", id = "id";

    public string $name;
    public string $declension; // parse to three declension strings ["článek", "články", "článků"]
    public string $created;
    public string $description;
    public string $last_edit;
    public int $id;
}