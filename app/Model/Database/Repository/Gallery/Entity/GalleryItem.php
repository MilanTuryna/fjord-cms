<?php


namespace App\Model\Database\Repository\Gallery\Entity;


use App\Model\Database\Entity;

/**
 * Class GalleryItem
 * @package App\Model\Database\Repository\Gallery\Entity
 */
class GalleryItem extends Entity
{
    const original_file = "original_file",
        compressed_file = "compressed_file",
        alt = "alt",
        image_description = "image_description",
        admin_id = "admin_id";

    public string $original_file;
    public string $compressed_file;
    public string $alt;
    public string $image_description;
    public int $admin_id;
    public int $id;
}