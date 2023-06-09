<?php

namespace App\Model\FileSystem\Gallery\Objects;

use App\Model\Database\Repository\Gallery\Entity\GalleryItem;

class GalleryItemFile extends GalleryItem
{
    public string $file_url;
}