<?php


namespace App\Forms\Gallery\Data;


use App\Model\Database\Repository\Gallery\Entity\GalleryItem;

class ItemFormData extends GalleryItem
{
    // 10MB
    const MAX_FILE_SIZE = 10*(1024**2);

    public mixed $file_content;
}