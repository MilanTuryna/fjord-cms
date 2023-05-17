<?php


namespace App\Model\Database\Repository\Dynamic\Entity;


use App\Model\Database\Entity;

class DynamicEntity extends Entity
{
    const name = "name", created = "created", description = "description",  id = "id";

    public string $name;
    public string $created;
    public string $description;
    public int $id;
}