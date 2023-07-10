<?php


namespace App\Model\Database\Repository\Gallery\Entity;


use App\Model\Database\Entity;

/**
 * Class Gallery
 * @package App\Model\Database\Repository\Gallery\Entity
 */
class Gallery extends Entity
{
    const name = "name", private = "private", miniature_url = "miniature_url", description = "description", uri = "uri", edited = "edited", created = "created", admin_id = "admin_id", id = "id";

    public string $name;
    public string $description;
    public string $uri;
    public string $edited;
    public string $miniature_url;
    public bool $private;
    public string $created;
    public int $admin_id;
    public int $id;
}