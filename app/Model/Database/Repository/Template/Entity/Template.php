<?php


namespace App\Model\Database\Repository\Template\Entity;


use App\Model\Database\Entity;

/**
 * Class Template
 * @package App\Model\Database\Repository\Template\Entity
 */
class Template extends Entity
{
    const title = "title", author_id = "author_id", help_link = "help_link", description = "description", version = "version", created = "created", edited = "edited"
, id = "id";

    public string $title;
    public int $author_id;
    public string $help_link;
    public string $description;
    public string $version;
    public string $created;
    public string $edited;
    public int $id;
}