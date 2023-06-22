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
        size_bytes = "size_bytes",
        resolution_x = "resolution_x",
        resolution_y = "resolution_y",
        image_description = "image_description",
        gallery_id = "gallery_id",
        admin_id = "admin_id";

    public string $original_file;
    public string $compressed_file;
    public int $size_bytes;
    public int $resolution_x;
    public int $resolution_y;
    public string $alt;
    public string $image_description;
    public int $gallery_id;
    public int $admin_id;
    public string $created;
    public int $id;
}