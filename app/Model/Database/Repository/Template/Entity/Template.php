<?php


namespace App\Model\Database\Repository\Template\Entity;


use App\Model\Database\Entity;

/**
 * Class Template
 * @package App\Model\Database\Repository\Template\Entity
 */
class Template extends Entity
{
    const title = "title", author_id = "author_id", used = "used", zip_name = "zip_name", website = "website", description = "description", version = "version", created = "created", edited = "edited"
, id = "id", dirname = "dirname", error404 = "error404";

    public string $title;
    public string $zip_name;
    public string $dirname;
    public string $dependencyPath; // representing relative path from index.json to folder with dependencies (for css,js...)
    public string $error404; // representing relative path from index.json to page 404 (Not Found)
    public int $author_id;
    public string $website;
    public string $description;
    public string $version;
    public string $created;
    public string $edited;
    public bool $used;
    public int $id;
}