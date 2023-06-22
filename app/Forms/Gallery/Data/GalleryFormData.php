<?php


namespace App\Forms\Gallery\Data;


use App\Model\Database\Repository\Gallery\Entity\Gallery;
use App\Model\Database\Repository\Gallery\Entity\GalleryItem;

/**
 * Class GalleryFormData
 * @package App\Forms\Gallery\Data
 */
class GalleryFormData extends Gallery
{
    public ?array $_global_upload;
}