<?php


namespace App\Model\Database\Repository\Product\Entity;

use App\Model\Database\Entity;

/**
 * Class Product
 * @package App\Model\Database\Repository\Product\Entity
 */
class Product extends Entity
{
    const title = "title", edited = "edited", miniature_url = "miniature_url", description = "description", uri = "uri", content = "content", gallery_id = "gallery_id", created = "created", active = "active", show = "show", priority = "priority", id = "id";

    public string $title;
    public string $miniature_url;
    public string $description;
    public string $uri;
    public string $content;
    public int $gallery_id;
    public string $created;
    public string $edited;
    public bool $active;
    public bool $show;
    public int $priority = 0;
    public int $id;
}