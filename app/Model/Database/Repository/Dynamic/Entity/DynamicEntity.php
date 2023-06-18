<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

/**
 * Class DynamicEntity
 * @package App\Model\Database\Repository\Dynamic\Entity
 */
class DynamicEntity extends Entity
{
    const name = "name", declension = "declension", created = "created", description = "description", generated_by = "generated_by", edited = "edited", id = "id";

    public string $name;
    public string $declension; // parse to three declension strings ["článek", "články", "článků"]
    public string $created;
    public string $edited;
    public string $description;
    public string $last_edit;
    public string $generated_by;
    public int $id;
}